<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoodLog;
use App\Models\Journal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MoodLogController extends Controller
{
    public function today()
    {
        $log = $this->getTodayLog();

        // Get current time to check if check-ins are allowed
        $currentHour = Carbon::now()->hour;
        $canCheckInMorning = $currentHour >= 6 && $currentHour < 12;
        $canCheckInEvening = $currentHour >= 12 && $currentHour < 18; // 12pm to 6pm

        // Get journal moods for consistency
        $journalMoods = Journal::MOODS;

        // Feature B: Check for reminder (if no check-in for 2+ days)
        $reminderData = $this->checkReminder();

        // Determine mood theme (prefer evening mood, fallback to morning mood)
        $moodTheme = $this->getMoodTheme($log);

        // Get user location for display
        $user = auth()->user();
        $userCity = $user->city ?? 'Dhaka';
        $userCountry = $user->country ?? 'Bangladesh';

        return view('mood.today', compact('log', 'canCheckInMorning', 'canCheckInEvening', 'journalMoods', 'reminderData', 'moodTheme', 'userCity', 'userCountry'));
    }


    public function saveMorning(Request $request)
    {
        // Check time restriction: 6am to 12pm
        $currentHour = Carbon::now()->hour;
        if ($currentHour < 6 || $currentHour >= 12) {
            return back()->with('error', 'Morning check-in is only available between 6:00 AM and 12:00 PM.');
        }

        $data = $request->validate([
            'morning_mood' => ['required', 'string', 'in:' . implode(',', array_keys(Journal::MOODS))],
            'planned_activities' => ['nullable', 'string', 'max:2000'],
        ]);

        $log = $this->getTodayLog();
        $log->update($data);

        return back()->with('status', 'Morning check-in saved 🌅');
    }

    public function saveEvening(Request $request)
    {
        // Check time restriction: 12pm to 6pm
        $currentHour = Carbon::now()->hour;
        if ($currentHour < 12 || $currentHour >= 18) {
            return back()->with('error', 'Evening check-in is only available between 12:00 PM and 6:00 PM.');
        }

        $data = $request->validate([
            'evening_mood' => ['required', 'string', 'in:' . implode(',', array_keys(Journal::MOODS))],
            'day_summary' => ['nullable', 'string', 'max:2000'],
            'was_active' => ['nullable', 'in:0,1'],
        ]);

        // convert "0"/"1" to boolean
        if (array_key_exists('was_active', $data)) {
            $data['was_active'] = $data['was_active'] === '1';
        }

        $log = $this->getTodayLog();
        $log->update($data);

        return back()->with('status', 'Evening check-out saved 🌙');
    }

    /**
     * Clear morning check-in
     */
    public function clearMorning(Request $request)
    {
        $log = $this->getTodayLog();
        $log->update([
            'morning_mood' => null,
            'planned_activities' => null,
        ]);

        return back()->with('status', 'Morning check-in cleared');
    }

    /**
     * Clear evening check-out
     */
    public function clearEvening(Request $request)
    {
        $log = $this->getTodayLog();
        $log->update([
            'evening_mood' => null,
            'day_summary' => null,
            'was_active' => null,
        ]);

        return back()->with('status', 'Evening check-out cleared');
    }

    /**
     * Get today's context (weather and air quality)
     */
    public function getContext(Request $request)
    {
        $user = auth()->user();
        
        // Get user's location or use defaults
        if ($user->city && $user->latitude && $user->longitude) {
            $city = $user->city;
            $country = $user->country ?? '';
            $latitude = (float) $user->latitude;
            $longitude = (float) $user->longitude;
        } else {
            // Default to Dhaka, Bangladesh
            $city = 'Dhaka';
            $country = 'Bangladesh';
            $latitude = 23.8103;
            $longitude = 90.4125;
        }

        // Get today's mood for mood-aware tips
        $log = $this->getTodayLog();
        $currentMood = $log->evening_mood ?? $log->morning_mood;

        // Get timezone from user's country or detect from coordinates
        $timezone = $this->detectTimezone($latitude, $longitude);
        $weather = $this->fetchWeather($latitude, $longitude, $timezone);
        // Pass city and country for more accurate district-level AQI
        $air = $this->fetchAirQuality($latitude, $longitude, $city, $country);
        $tips = $this->generateTips($weather, $air, $currentMood);
        
        // Get emotional context for air quality and weather, combine into one message
        $airEmotionalContext = $this->getAirQualityEmotionalContext($air);
        $weatherEmotionalContext = $this->getWeatherEmotionalContext($weather);
        $combinedEmotionalContext = $this->combineEmotionalContext($airEmotionalContext, $weatherEmotionalContext);
        
        // Determine if we should show health mode warning
        $healthMode = $this->shouldShowHealthMode($currentMood, $air);

        // Always return city, even if weather/air fail
        return response()->json([
            'city' => $city,
            'country' => $country,
            'weather' => $weather,
            'air' => $air,
            'tips' => $tips,
            'health_mode' => $healthMode,
            'current_mood' => $currentMood,
            'emotional_context' => $combinedEmotionalContext,
        ], 200);
    }

    /**
     * Update user location
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        
        // Geocode city and country to get coordinates
        $coordinates = $this->geocodeLocation($request->city, $request->country);
        
        if ($coordinates) {
            $user->update([
                'city' => $request->city,
                'country' => $request->country,
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'city' => $user->city,
                'country' => $user->country,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Could not find coordinates for this location',
        ], 400);
    }

    /**
     * Geocode city and country to get coordinates using Open-Meteo Geocoding API
     */
    private function geocodeLocation(string $city, string $country): ?array
    {
        try {
            // Try searching with city and country together first
            $query = "{$city}, {$country}";
            $response = Http::timeout(5)->get('https://geocoding-api.open-meteo.com/v1/search', [
                'name' => $query,
                'count' => 10,
                'language' => 'en',
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['results']) && count($data['results']) > 0) {
                    // Filter by country name (case-insensitive)
                    $filtered = array_filter($data['results'], function($r) use ($country) {
                        $resultCountry = $r['country'] ?? '';
                        $resultAdmin = $r['admin1'] ?? '';
                        return stripos($resultCountry, $country) !== false || 
                               stripos($resultAdmin, $country) !== false ||
                               stripos($country, $resultCountry) !== false;
                    });
                    
                    // Use filtered result if found, otherwise use first result
                    $result = count($filtered) > 0 ? reset($filtered) : $data['results'][0];
                    
                    return [
                        'latitude' => (float) $result['latitude'],
                        'longitude' => (float) $result['longitude'],
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Geocoding error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch weather from Open-Meteo API
     */
    private function fetchWeather(float $latitude, float $longitude, ?string $timezone = null): ?array
    {
        try {
            // Auto-detect timezone if not provided
            if (!$timezone) {
                $timezone = $this->detectTimezone($latitude, $longitude);
            }
            
            $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,precipitation,weather_code',
                'timezone' => $timezone,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $current = $data['current'] ?? null;

                if ($current) {
                    $tempC = round($current['temperature_2m'] ?? 0);
                    $precipitation = $current['precipitation'] ?? 0;
                    $humidity = $current['relative_humidity_2m'] ?? null;
                    $weatherCode = $current['weather_code'] ?? 0;

                    // Determine condition from weather code
                    $condition = $this->getWeatherCondition($weatherCode, $precipitation);
                    $isRainy = $precipitation > 0 || in_array($weatherCode, [61, 63, 65, 66, 67, 80, 81, 82]);

                    return [
                        'temp_c' => $tempC,
                        'condition' => $condition,
                        'is_rainy' => $isRainy,
                        'humidity' => $humidity,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Open-Meteo API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Detect timezone from coordinates (simple approximation)
     */
    private function detectTimezone(float $latitude, float $longitude): string
    {
        // Simple timezone detection based on longitude
        // More accurate would require a timezone database, but this works for most cases
        $offset = round($longitude / 15);
        
        // Common timezones by region
        if ($latitude >= 20 && $latitude <= 30 && $longitude >= 88 && $longitude <= 93) {
            return 'Asia/Dhaka'; // Bangladesh
        } elseif ($latitude >= 6 && $latitude <= 37 && $longitude >= 68 && $longitude <= 97) {
            return 'Asia/Kolkata'; // India
        } elseif ($latitude >= 0 && $latitude <= 50 && $longitude >= 100 && $longitude <= 150) {
            return 'Asia/Shanghai'; // East Asia
        } elseif ($latitude >= 25 && $latitude <= 50 && $longitude >= -125 && $longitude <= -65) {
            return 'America/New_York'; // US East
        } elseif ($latitude >= 25 && $latitude <= 50 && $longitude >= -125 && $longitude <= -100) {
            return 'America/Los_Angeles'; // US West
        } elseif ($latitude >= 35 && $latitude <= 70 && $longitude >= -10 && $longitude <= 40) {
            return 'Europe/London'; // Europe
        }
        
        // Default to UTC offset
        return 'UTC';
    }

    /**
     * Get weather condition label from weather code
     */
    private function getWeatherCondition(int $code, float $precipitation): string
    {
        // WMO Weather interpretation codes (WW)
        if ($precipitation > 0) {
            return 'Rain';
        }

        $conditions = [
            0 => 'Clear',
            1 => 'Clear',
            2 => 'Cloudy',
            3 => 'Cloudy',
            45 => 'Fog',
            48 => 'Fog',
            51 => 'Drizzle',
            53 => 'Drizzle',
            55 => 'Drizzle',
            56 => 'Drizzle',
            57 => 'Drizzle',
            61 => 'Rain',
            63 => 'Rain',
            65 => 'Rain',
            66 => 'Rain',
            67 => 'Rain',
            71 => 'Snow',
            73 => 'Snow',
            75 => 'Snow',
            77 => 'Snow',
            80 => 'Rain',
            81 => 'Rain',
            82 => 'Rain',
            85 => 'Snow',
            86 => 'Snow',
            95 => 'Thunderstorm',
            96 => 'Thunderstorm',
            99 => 'Thunderstorm',
        ];

        return $conditions[$code] ?? 'Clear';
    }

    /**
     * Fetch air quality from AQICN API
     * Tries city name first (more accurate for district-level), then falls back to coordinates
     */
    private function fetchAirQuality(float $latitude, float $longitude, ?string $city = null, ?string $country = null): ?array
    {
        $token = env('AQICN_TOKEN', '8860c501daca85e76c0235b630e0ace95a9654c4');
        
        if (empty($token)) {
            Log::warning('AQICN_TOKEN not set in .env');
            return null;
        }

        try {
            $response = null;
            $useCityResponse = false;
            
            // Try city name first for more accurate district-level data
            // City name queries return the main station for that city, which is more accurate
            if ($city) {
                // Format city name for API (lowercase, no special encoding needed)
                $cityQuery = strtolower(trim($city));
                
                // Try city name endpoint first (most accurate for district-level)
                $url = sprintf("https://api.waqi.info/feed/%s/", $cityQuery);
                $cityResponse = Http::timeout(10)->get($url, [
                    'token' => $token,
                ]);
                
                // If city name query succeeds and has valid AQI data, use it
                if ($cityResponse->successful()) {
                    $cityData = $cityResponse->json();
                    // Check if we got valid AQI data
                    if (isset($cityData['status']) && $cityData['status'] === 'ok' && 
                        isset($cityData['data']['aqi']) && $cityData['data']['aqi'] > 0) {
                        $response = $cityResponse;
                        $useCityResponse = true;
                        // Log which city station we got
                        $stationName = $cityData['data']['city']['name'] ?? $city;
                        Log::info('AQI fetched by city name', [
                            'city' => $city, 
                            'station' => $stationName, 
                            'aqi' => $cityData['data']['aqi']
                        ]);
                    }
                }
            }
            
            // Fall back to geo coordinates if city name didn't work
            if (!$useCityResponse) {
                $url = sprintf("https://api.waqi.info/feed/geo:%.6f;%.6f/", $latitude, $longitude);
                $response = Http::timeout(10)->get($url, [
                    'token' => $token,
                ]);
                Log::info('AQI fetched by coordinates', ['lat' => $latitude, 'lon' => $longitude]);
            }

            if ($response->successful()) {
                $data = $response->json();
                
                // Check for successful response
                if (isset($data['status']) && $data['status'] === 'ok' && isset($data['data'])) {
                    // AQI is in data.data.aqi (nested structure)
                    $aqi = null;
                    
                    // Primary path: data.data.aqi (this is the correct path based on API docs)
                    if (isset($data['data']['aqi'])) {
                        $aqiValue = $data['data']['aqi'];
                        // Convert to integer, handling both string and numeric values
                        if (is_numeric($aqiValue)) {
                            $aqi = (int) round((float) $aqiValue);
                        }
                    }
                    
                    // Fallback: check if AQI is directly in data (some API versions)
                    if (($aqi === null || $aqi === 0) && isset($data['aqi'])) {
                        $aqiValue = $data['aqi'];
                        if (is_numeric($aqiValue)) {
                            $aqi = (int) round((float) $aqiValue);
                        }
                    }
                    
                    // If still null or 0, try to get from iaqi.pm25.v (PM2.5 value) as fallback
                    if (($aqi === null || $aqi === 0) && isset($data['data']['iaqi']['pm25']['v'])) {
                        $pm25Value = $data['data']['iaqi']['pm25']['v'];
                        // PM2.5 to AQI conversion (US EPA formula)
                        if (is_numeric($pm25Value)) {
                            $pm25 = (float) $pm25Value;
                            if ($pm25 <= 12) {
                                $aqi = (int) round(($pm25 / 12) * 50);
                            } elseif ($pm25 <= 35.4) {
                                $aqi = (int) round(50 + (($pm25 - 12) / (35.4 - 12)) * 50);
                            } elseif ($pm25 <= 55.4) {
                                $aqi = (int) round(100 + (($pm25 - 35.4) / (55.4 - 35.4)) * 50);
                            } elseif ($pm25 <= 150.4) {
                                $aqi = (int) round(150 + (($pm25 - 55.4) / (150.4 - 55.4)) * 100);
                            } else {
                                $aqi = (int) round(250 + (($pm25 - 150.4) / (250.4 - 150.4)) * 100);
                            }
                        }
                    }
                    
                    if ($aqi !== null && $aqi > 0) {
                        $level = $this->getAQILevel($aqi);

                        return [
                            'aqi' => $aqi,
                            'level' => $level,
                        ];
                    } else {
                        Log::warning('AQI value is null, zero, or invalid', [
                            'aqi' => $aqi,
                            'has_data_aqi' => isset($data['data']['aqi']),
                            'data_aqi_value' => $data['data']['aqi'] ?? null,
                        ]);
                    }
                } else {
                    $status = $data['status'] ?? 'unknown';
                    if ($status !== 'ok') {
                        Log::warning('AQICN API returned non-ok status', ['status' => $status]);
                    }
                }
            } else {
                Log::warning('AQICN API request failed', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 200)
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('AQICN API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get AQI level label
     */
    private function getAQILevel(int $aqi): string
    {
        if ($aqi <= 50) return 'Good';
        if ($aqi <= 100) return 'Moderate';
        if ($aqi <= 150) return 'Unhealthy for Sensitive Groups';
        if ($aqi <= 200) return 'Unhealthy';
        if ($aqi <= 300) return 'Very Unhealthy';
        return 'Hazardous';
    }

    /**
     * Generate emotionally-aware tips based on weather and air quality
     */
    private function generateTips(?array $weather, ?array $air, ?string $currentMood = null): array
    {
        $todayTip = '';

        // Priority order: AQI > Temperature > Rain > Good weather
        if ($air && $air['aqi'] >= 150) {
            $todayTip = '💚 Poor air quality can affect mood and focus. A calm indoor activity might help today.';
        } elseif ($air && $air['aqi'] >= 100) {
            $todayTip = '🌿 The air is a bit heavy today. It\'s okay to slow down — indoor rest can support your mood.';
        } elseif ($weather && $weather['temp_c'] >= 32) {
            $todayTip = "☀️ It's hot today — your body might feel more tired. Drink water and take gentle breaks.";
        } elseif ($weather && $weather['is_rainy']) {
            $todayTip = '🌧️ Rainy days can feel cozy or heavy. Keep plans light and be kind to yourself.';
        } elseif ($air && $air['aqi'] < 100 && $weather && $weather['temp_c'] < 28) {
            $todayTip = '🌤️ Nice weather today — a short walk might lift your spirits.';
        } else {
            $todayTip = '💙 Take care of yourself today, however you\'re feeling.';
        }

        return [
            'today_tip' => $todayTip,
        ];
    }

    /**
     * Get emotional context for air quality
     */
    private function getAirQualityEmotionalContext(?array $air): ?string
    {
        if (!$air || !isset($air['aqi'])) {
            return null;
        }

        $aqi = $air['aqi'];
        
        if ($aqi >= 200) {
            return "The air quality today may make you feel tired, foggy, or low-energy. Many people feel less motivated on days like this.";
        } elseif ($aqi >= 150) {
            return "It's a heavy, polluted day — you might feel less energetic or a bit foggy. That's completely normal.";
        } elseif ($aqi >= 100) {
            return "The air is a bit heavy today. Some people feel slightly less energetic when air quality is moderate.";
        } elseif ($aqi >= 50) {
            return "The air quality is okay today, but some sensitive people might feel a slight difference.";
        }
        
        return null; // Good air quality doesn't need emotional context
    }

    /**
     * Get emotional context for weather
     */
    private function getWeatherEmotionalContext(?array $weather): ?string
    {
        if (!$weather) {
            return null;
        }

        $temp = $weather['temp_c'] ?? null;
        $isRainy = $weather['is_rainy'] ?? false;
        $condition = strtolower($weather['condition'] ?? '');

        if ($isRainy || strpos($condition, 'rain') !== false) {
            return "It's a gloomy, rainy day — some people feel cozier indoors, while others might feel a bit low-energy.";
        } elseif ($temp !== null && $temp >= 32) {
            return "It's very hot today — heat can make you feel more tired or irritable. That's your body protecting itself.";
        } elseif ($temp !== null && $temp <= 15) {
            return "It's quite cold today — some people feel more sluggish or want to stay cozy when it's chilly.";
        } elseif (strpos($condition, 'cloud') !== false || strpos($condition, 'overcast') !== false) {
            return "It's a cloudy day — gray skies can sometimes make people feel a bit quieter or more reflective.";
        }
        
        return null;
    }

    /**
     * Combine air quality and weather emotional contexts into one message
     */
    private function combineEmotionalContext(?string $airContext, ?string $weatherContext): ?string
    {
        $parts = [];
        
        if ($airContext) {
            $parts[] = $airContext;
        }
        
        if ($weatherContext) {
            $parts[] = $weatherContext;
        }
        
        if (empty($parts)) {
            return null;
        }
        
        // Combine with a gentle connector
        if (count($parts) === 2) {
            return $parts[0] . ' ' . $parts[1];
        }
        
        return $parts[0];
    }

    /**
     * Determine if health mode warning should be shown
     */
    private function shouldShowHealthMode(?string $currentMood, ?array $air): bool
    {
        // Show health mode if mood is tired/anxious AND AQI is poor (>= 150)
        if (!$currentMood || !$air) {
            return false;
        }

        $sensitiveMoods = ['tired', 'anxious'];
        return in_array($currentMood, $sensitiveMoods) && $air['aqi'] >= 150;
    }

    /**
     * Feature B: Check if reminder should be shown (for initial page load)
     */
    private function checkReminder()
    {
        $daysSince = $this->getDaysSinceLastCheckin();

        if ($daysSince < 2) {
            return null;
        }

        return [
            'show' => true,
            'message' => "We haven't heard from you in a couple of days. Want a quick check-in?",
        ];
    }

    /**
     * Get days since last mood check-in
     */
    private function getDaysSinceLastCheckin(): int
    {
        $userId = auth()->id();

        $lastLog = MoodLog::where('user_id', $userId)
            ->where(function($query) {
                $query->whereNotNull('morning_mood')
                      ->orWhereNotNull('evening_mood');
            })
            ->orderBy('log_date', 'desc')
            ->first();

        if (!$lastLog) {
            // No check-ins ever
            return 999; // Large number to trigger reminder
        }

        $lastDate = Carbon::parse($lastLog->log_date);
        $today = Carbon::today();

        return $today->diffInDays($lastDate);
    }

    /**
     * Get mood theme based on today's mood
     * Prefers evening mood, falls back to morning mood, then default
     */
    private function getMoodTheme($log): string
    {
        // Prefer evening mood, fallback to morning mood
        $mood = $log->evening_mood ?? $log->morning_mood;

        if (!$mood) {
            return 'default';
        }

        // Map mood to theme
        $themeMap = [
            'happy' => 'happy',
            'excited' => 'happy',
            'calm' => 'calm',
            'neutral' => 'neutral',
            'sad' => 'sad',
            'anxious' => 'anxious',
            'tired' => 'anxious',
            'angry' => 'angry',
        ];

        return $themeMap[$mood] ?? 'default';
    }

    private function getTodayLog()
    {
        $userId = auth()->id();

        // Important: matches date even if DB stores datetime (2025-12-27 00:00:00)
        $log = MoodLog::where('user_id', $userId)
            ->whereDate('log_date', Carbon::today())
            ->first();

        if (!$log) {
            $log = MoodLog::create([
                'user_id' => $userId,
                'log_date' => Carbon::today()->startOfDay(),
            ]);
        }

        return $log;
    }

}
