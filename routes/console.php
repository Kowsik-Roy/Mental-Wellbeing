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
    $from = now()->subWeek()->startOfWeek();
    $to = now()->subWeek()->endOfWeek();
    $periodLabel = $from->format('M d, Y') . ' - ' . $to->format('M d, Y');

    User::chunk(100, function ($users) use ($from, $to, $periodLabel) {
        foreach ($users as $user) {
            // Mood stats for last week
            $moodStats = Journal::where('user_id', $user->id)
                ->whereBetween('created_at', [$from, $to])
                ->whereNotNull('mood')
                ->selectRaw('mood, count(*) as count')
                ->groupBy('mood')
                ->get();

            // Habit stats for current week (uses existing helper)
            $habitStats = [];
            $habits = Habit::where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            foreach ($habits as $habit) {
                $habitStats[] = [
                    'title' => $habit->title,
                    'weekly_completion' => round($habit->getWeeklyCompletionPercentage(), 1),
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

// Schedule: run every Monday at 9 AM server time
Schedule::command('weekly:send-summaries')->weeklyOn(1, '9:00');

