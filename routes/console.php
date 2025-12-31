<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Journal;
use App\Models\Habit;
use App\Mail\WeeklySummaryMail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('weekly:send-summaries', function () {
    // Use last 7 days (rolling 7 days from today, including today)
    $now = \Carbon\Carbon::now('Asia/Dhaka');
    $from = $now->copy()->subDays(6)->startOfDay(); // Last 7 days including today
    $to = $now->copy()->endOfDay();
    $periodLabel = $from->format('M d, Y') . ' - ' . $to->format('M d, Y');

    User::chunk(100, function ($users) use ($from, $to, $periodLabel) {
        foreach ($users as $user) {
            // Mood stats for last 7 days
            // Query by date range, converting UTC timestamps to Asia/Dhaka dates
            $fromDate = $from->format('Y-m-d');
            $toDate = $to->format('Y-m-d');
            
            $moodStats = Journal::where('user_id', $user->id)
                ->whereNotNull('mood')
                ->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+06:00')) >= ?", [$fromDate])
                ->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+06:00')) <= ?", [$toDate])
                ->selectRaw('mood, count(*) as count')
                ->groupBy('mood')
                ->get();

            // Habit stats for last 7 days
            $habitStats = [];
            $habits = Habit::where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            foreach ($habits as $habit) {
                // Calculate completion percentage for the last 7 days
                $completionPercentage = $habit->getCompletionPercentageForRange($from, $to);
                
                $habitStats[] = [
                    'title' => $habit->title,
                    'weekly_completion' => round($completionPercentage, 1),
                    'current_streak' => $habit->current_streak ?? 0,
                    'best_streak' => $habit->best_streak ?? 0,
                ];
            }

            // Only send if there is something meaningful to show
            if ($moodStats->count() > 0 || count($habitStats) > 0) {
                Mail::to($user->email)->send(
                    new WeeklySummaryMail($user, $moodStats, $habitStats, $periodLabel)
                );
            }
        }
    });
})->purpose('Send weekly mood and habit summaries to all users');

// Schedule: run every Saturday at 9 PM (Asia/Dhaka timezone)
Schedule::command('weekly:send-summaries')->weeklyOn(6, '21:00')->timezone('Asia/Dhaka');

// Schedule: check for habit reminders every minute
Schedule::command('habits:send-reminders')->everyMinute();

