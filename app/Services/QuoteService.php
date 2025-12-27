<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class QuoteService
{
    /**
     * Get a daily quote for a specific user.
     * Each user gets their own quote that refreshes daily.
     * 
     * @param int|null $userId User ID, or null for guest/anonymous users
     * @return array
     */
    public function getDailyQuote(?int $userId = null): array
    {
        // Create user-specific cache key
        $cacheKey = $userId 
            ? "daily_quote_user_{$userId}_" . now()->toDateString()
            : "daily_quote_guest_" . now()->toDateString();
        
        // Cache until end of day (midnight) so it refreshes daily
        $expiresAt = now()->endOfDay();
        
        return Cache::remember($cacheKey, $expiresAt, function () use ($userId) {
            try {
                // Fetch a random quote from zenquotes API
                $response = Http::timeout(5)->get('https://zenquotes.io/api/random');
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data[0]) && isset($data[0]['q'])) {
                        return [
                            'text' => $data[0]['q'],
                            'author' => $data[0]['a'] ?? 'Unknown'
                        ];
                    }
                }
                
                // Fallback if API fails
                return $this->getFallbackQuote($userId);
            } catch (\Exception $e) {
                // Fallback on exception
                return $this->getFallbackQuote($userId);
            }
        });
    }
    
    /**
     * Get a fallback quote when API is unavailable.
     * Uses user ID to provide some variation.
     * 
     * @param int|null $userId
     * @return array
     */
    private function getFallbackQuote(?int $userId): array
    {
        $fallbackQuotes = [
            ['text' => 'Small steps matter.', 'author' => 'Mental Wellness Companion'],
            ['text' => 'Progress, not perfection.', 'author' => 'Mental Wellness Companion'],
            ['text' => 'You are stronger than you think.', 'author' => 'Mental Wellness Companion'],
            ['text' => 'Every day is a fresh start.', 'author' => 'Mental Wellness Companion'],
            ['text' => 'Believe in yourself.', 'author' => 'Mental Wellness Companion'],
        ];
        
        // Use user ID to select a consistent fallback quote for the day
        $index = $userId ? ($userId % count($fallbackQuotes)) : 0;
        
        return $fallbackQuotes[$index];
    }
}