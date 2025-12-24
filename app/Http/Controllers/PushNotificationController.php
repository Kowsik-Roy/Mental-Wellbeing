<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    /**
     * Subscribe to push notifications.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();

        // Check if subscription already exists
        $subscription = PushSubscription::where('endpoint', $request->endpoint)
            ->where('user_id', $user->id)
            ->first();

        if ($subscription) {
            // Update existing subscription
            $subscription->update([
                'public_key' => $request->keys['p256dh'],
                'auth_token' => $request->keys['auth'],
                'user_agent' => $request->userAgent(),
            ]);
        } else {
            // Create new subscription
            PushSubscription::create([
                'user_id' => $user->id,
                'endpoint' => $request->endpoint,
                'public_key' => $request->keys['p256dh'],
                'auth_token' => $request->keys['auth'],
                'user_agent' => $request->userAgent(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Subscribed to push notifications']);
    }

    /**
     * Unsubscribe from push notifications.
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        $user = Auth::user();

        PushSubscription::where('endpoint', $request->endpoint)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Unsubscribed from push notifications']);
    }

    /**
     * Check for incomplete habits with reminders (for frontend polling).
     */
    public function checkReminders(Request $request)
    {
        $user = Auth::user();
        // Use Asia/Dhaka timezone explicitly
        $now = \Carbon\Carbon::now('Asia/Dhaka');

        // Get active habits with reminder times
        $habits = \App\Models\Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->whereNotNull('reminder_time')
            ->get();

        $reminders = [];

        foreach ($habits as $habit) {
            // Parse reminder time - handle different formats
            $reminderTimeStr = is_string($habit->reminder_time) 
                ? $habit->reminder_time 
                : $habit->reminder_time->format('H:i:s');
            
            // Extract just the time part (H:i)
            $timeParts = explode(':', $reminderTimeStr);
            $reminderHour = (int)$timeParts[0];
            $reminderMinute = (int)$timeParts[1];
            
            // Create reminder time for today in Asia/Dhaka timezone
            $reminderTime = \Carbon\Carbon::create(
                $now->year,
                $now->month,
                $now->day,
                $reminderHour,
                $reminderMinute,
                0,
                'Asia/Dhaka'
            );

            // Calculate minutes since reminder time (negative = before, positive = after)
            $minutesSinceReminder = $reminderTime->diffInMinutes($now, false);

            // Only trigger in a small window at/after the reminder time
            // - Before the time: don't notify
            // - 0 to <2 minutes after time: notify
            // - 2+ minutes after: don't notify (time passed)
            if ($minutesSinceReminder < 0) {
                // Reminder time not reached yet
                continue;
            }
            if ($minutesSinceReminder >= 2) {
                // Reminder window passed
                continue;
            }

            // Check if habit should be active today
            if (!$this->isHabitActiveToday($habit, $now)) {
                continue;
            }

            // Check if habit is already completed today
            $todayLog = \App\Models\HabitLog::where('habit_id', $habit->id)
                ->whereDate('logged_date', $now->toDateString())
                ->where('completed', true)
                ->first();

            if (!$todayLog) {
                $reminders[] = [
                    'id' => $habit->id,
                    'title' => $habit->title,
                ];
            }
        }

        return response()->json([
            'has_reminders' => count($reminders) > 0,
            'reminders' => $reminders,
        ]);
    }

    /**
     * Check if habit should be active today based on frequency.
     */
    private function isHabitActiveToday(\App\Models\Habit $habit, \Carbon\Carbon $date): bool
    {
        switch ($habit->frequency) {
            case 'daily':
                return true;
            case 'weekdays':
                return $date->isWeekday();
            case 'weekend':
                return $date->isWeekend();
            default:
                return true;
        }
    }
}
