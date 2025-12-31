<?php

namespace App\Services;

use App\Models\User;
use App\Models\Journal;
use App\Models\EmergencyContact;
use App\Mail\EmergencyAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmergencyAlertService
{
    /**
     * Check all users for 3 consecutive days of sad mood and send alerts.
     */
    public function checkAndSendAlerts(): void
    {
        $users = User::whereHas('emergencyContact')->get();

        foreach ($users as $user) {
            if ($this->hasConsecutiveSadMoods($user, 3)) {
                $this->sendEmergencyAlert($user);
            }
        }
    }

    /**
     * Check if user has consecutive sad moods.
     * 
     * @param User $user
     * @param int $days Number of consecutive days
     * @return bool
     */
    public function hasConsecutiveSadMoods(User $user, int $days = 3): bool
    {
        // Get the last N days of journal entries (use Asia/Dhaka timezone)
        $now = Carbon::now('Asia/Dhaka');
        $startDate = $now->copy()->subDays($days - 1)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        // Get journal entries for the last N days, ordered by date
        $journals = Journal::where('user_id', $user->id)
            ->whereNotNull('mood')
            ->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+06:00')) >= ?", [$startDate->format('Y-m-d')])
            ->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+06:00')) <= ?", [$endDate->format('Y-m-d')])
            ->orderByRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+06:00')) ASC")
            ->get();

        // Group by date
        $moodsByDate = [];
        foreach ($journals as $journal) {
            $date = Carbon::parse($journal->created_at)->setTimezone('Asia/Dhaka')->format('Y-m-d');
            // If multiple entries on same day, take the last one (most recent mood of the day)
            $moodsByDate[$date] = $journal->mood;
        }

        // Check for consecutive sad moods
        $consecutiveSadDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            
            // Check if there's a mood entry for this date
            if (isset($moodsByDate[$dateKey])) {
                if ($moodsByDate[$dateKey] === 'sad') {
                    $consecutiveSadDays++;
                    if ($consecutiveSadDays >= $days) {
                        return true;
                    }
                } else {
                    // Reset counter if mood is not sad
                    $consecutiveSadDays = 0;
                }
            } else {
                // No entry for this day, reset counter
                $consecutiveSadDays = 0;
            }

            $currentDate->addDay();
        }

        return false;
    }

    /**
     * Send emergency alert to user's emergency contact.
     * 
     * @param User $user
     */
    public function sendEmergencyAlert(User $user): void
    {
        $emergencyContact = $user->emergencyContact()->first();

        if (!$emergencyContact) {
            Log::warning("Emergency alert attempted for user {$user->id} but no emergency contact found");
            return;
        }

        // Check if we've already sent an alert today to avoid spam
        $today = Carbon::today('Asia/Dhaka')->format('Y-m-d');
        $cacheKey = "emergency_alert_sent_{$user->id}_{$today}";

        if (cache()->has($cacheKey)) {
            Log::info("Emergency alert already sent today for user {$user->id}");
            return;
        }

        try {
            Mail::to($emergencyContact->email)->send(
                new EmergencyAlertMail($user, $emergencyContact)
            );

            // Mark as sent for today
            cache()->put($cacheKey, true, Carbon::now()->endOfDay());

            Log::info("Emergency alert sent to {$emergencyContact->email} for user {$user->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send emergency alert for user {$user->id}: " . $e->getMessage());
        }
    }
}
