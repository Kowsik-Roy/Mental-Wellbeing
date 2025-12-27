<?php

namespace App\Console\Commands;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\PushSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendHabitReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'habits:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notifications for incomplete habits with reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        
        $this->info("Checking habits for reminders at {$currentTime}...");

        // Get all active habits with reminder times
        $habits = Habit::where('is_active', true)
            ->whereNotNull('reminder_time')
            ->with('user')
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($habits as $habit) {
            // Parse reminder time
            $reminderTime = is_string($habit->reminder_time) 
                ? Carbon::createFromFormat('H:i:s', $habit->reminder_time)
                : Carbon::parse($habit->reminder_time);
            
            $reminderTimeStr = $reminderTime->format('H:i');
            
            // Check if it's time for this reminder (within 1 minute window)
            $timeDiff = abs($now->diffInMinutes($reminderTime));
            if ($timeDiff > 1) {
                continue; // Not time for this reminder yet
            }

            // Check if habit should be active today based on frequency
            if (!$this->isHabitActiveToday($habit, $now)) {
                $this->line("   Skipping {$habit->title} - not active today (frequency: {$habit->frequency})");
                $skippedCount++;
                continue;
            }

            // Check if habit is already completed today
            $todayLog = HabitLog::where('habit_id', $habit->id)
                ->whereDate('logged_date', $now->toDateString())
                ->where('completed', true)
                ->first();

            if ($todayLog) {
                $this->line("   Skipping {$habit->title} - already completed today");
                $skippedCount++;
                continue;
            }

            // Get user's push subscriptions
            $subscriptions = PushSubscription::where('user_id', $habit->user_id)->get();

            if ($subscriptions->isEmpty()) {
                $this->line("   Skipping {$habit->title} - no push subscriptions for user");
                $skippedCount++;
                continue;
            }

            // Send notification to all user's devices
            foreach ($subscriptions as $subscription) {
                $this->sendPushNotification($subscription, $habit);
            }

            $this->info("   ✓ Sent reminder for: {$habit->title}");
            $sentCount++;
        }

        $this->newLine();
        $this->info("Summary: {$sentCount} reminders sent, {$skippedCount} skipped");
        
        return 0;
    }

    /**
     * Check if habit should be active today based on frequency.
     */
    private function isHabitActiveToday(Habit $habit, Carbon $date): bool
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

    /**
     * Send push notification using Web Push API.
     */
    private function sendPushNotification(PushSubscription $subscription, Habit $habit)
    {
        try {
            // Use Laravel's HTTP client to send push notification
            // Note: This requires a push service (like web-push-php library)
            // For now, we'll use a simple approach with the browser's Push API
            
            $payload = json_encode([
                'title' => 'Habit Reminder',
                'body' => "Don't forget to complete: {$habit->title}",
                'icon' => '/favicon.ico',
                'badge' => '/favicon.ico',
                'data' => [
                    'url' => route('habits.index'),
                    'habit_id' => $habit->id,
                ],
            ]);

            // Send to push service endpoint
            $response = \Illuminate\Support\Facades\Http::timeout(5)->post($subscription->endpoint, [
                'payload' => $payload,
            ], [
                'Authorization' => 'key=' . $subscription->public_key,
                'Crypto-Key' => 'p256dh=' . $subscription->public_key . ';auth=' . $subscription->auth_token,
            ]);

            if (!$response->successful()) {
                Log::warning("Failed to send push notification", [
                    'subscription_id' => $subscription->id,
                    'habit_id' => $habit->id,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error sending push notification: " . $e->getMessage(), [
                'subscription_id' => $subscription->id,
                'habit_id' => $habit->id,
            ]);
        }
    }
}
