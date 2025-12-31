<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Journal;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Mail\WeeklySummaryMail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('weekly:send-summaries', function () {
    // Calculate previous full week: Monday to Sunday (matching habit's week definition)
    // Week starts on Monday and ends on Sunday
    // Use Asia/Dhaka timezone explicitly
    $now = \Carbon\Carbon::now('Asia/Dhaka');
    
    // Get the previous week (Monday to Sunday)
    // If today is Monday, we want last Monday to last Sunday
    // If today is any other day, we want the most recent Monday-Sunday period
    $lastMonday = $now->copy()->subWeek()->startOfWeek(\Carbon\Carbon::MONDAY);
    $lastSunday = $lastMonday->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
    
    $from = $lastMonday->startOfDay();
    $to = $lastSunday->endOfDay();
    $periodLabel = $from->format('M d, Y') . ' - ' . $to->format('M d, Y');

    User::chunk(100, function ($users) use ($from, $to, $periodLabel) {
        foreach ($users as $user) {
            // Mood stats for last week
            // Query by date range, converting UTC timestamps to Asia/Dhaka dates
            // Use CONVERT_TZ to convert created_at from UTC to Asia/Dhaka, then filter by date
            $fromDate = $from->format('Y-m-d');
            $toDate = $to->format('Y-m-d');
            
            $moodStats = Journal::where('user_id', $user->id)
                ->whereNotNull('mood')
                ->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+06:00')) >= ?", [$fromDate])
                ->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+06:00')) <= ?", [$toDate])
                ->selectRaw('mood, count(*) as count')
                ->groupBy('mood')
                ->get();

            // Habit stats for the Monday-Sunday week period
            $habitStats = [];
            $habits = Habit::where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            foreach ($habits as $habit) {
                // Calculate completion percentage for the Monday-Sunday week period
                $completedDays = 0;
                $expectedDays = 0;
                
                // Iterate through each day in the period
                $currentDate = $from->copy();
                while ($currentDate <= $to) {
                    // Check if this date is active for the habit's frequency
                    $isActive = false;
                    if ($habit->frequency === 'daily') {
                        $isActive = true;
                    } elseif ($habit->frequency === 'weekdays') {
                        $isActive = $currentDate->isWeekday();
                    } elseif ($habit->frequency === 'weekend') {
                        $isActive = $currentDate->isWeekend();
                    }
                    
                    if ($isActive) {
                        $expectedDays++;
                        // Check if habit was completed on this day
                        $hasCompleted = HabitLog::where('habit_id', $habit->id)
                            ->where('user_id', $user->id)
                            ->whereDate('logged_date', $currentDate->format('Y-m-d'))
                            ->where('completed', true)
                            ->exists();
                        
                        if ($hasCompleted) {
                            $completedDays++;
                        }
                    }
                    
                    $currentDate->addDay();
                }
                
                $completionPercentage = $expectedDays > 0 
                    ? round(($completedDays / $expectedDays) * 100, 1) 
                    : 0;
                
                $habitStats[] = [
                    'title' => $habit->title,
                    'weekly_completion' => $completionPercentage,
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

// Schedule: run every Monday at 9 AM server time (matching habit's week definition)
Schedule::command('weekly:send-summaries')->weeklyOn(1, '9:00');

// Schedule: check for habit reminders every minute
Schedule::command('habits:send-reminders')->everyMinute();

