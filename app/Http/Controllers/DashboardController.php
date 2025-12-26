<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Journal;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Mail\WeeklySummaryMail;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Send the weekly email summary to the current user.
     */
    public function sendSummary(Request $request)
    {
        $user = $request->user();

        // Use last 7 days (including today)
        $from = now()->subDays(6)->startOfDay(); // 6 days ago to today = 7 days total
        $to = now()->endOfDay();
        $periodLabel = $from->format('M d, Y') . ' - ' . $to->format('M d, Y');

        // Mood stats for the period
        $moodStats = Journal::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('mood')
            ->selectRaw('mood, count(*) as count')
            ->groupBy('mood')
            ->get();

        // Habit stats for the last 7 days
        $habitStats = [];
        $habits = Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($habits as $habit) {
            // Calculate completion percentage for the last 7 days
            $completedDays = 0;
            $expectedDays = 0;
            
            // Count expected days based on frequency
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->startOfDay();
                
                // Check if this date is active for the habit's frequency
                $isActive = false;
                if ($habit->frequency === 'daily') {
                    $isActive = true;
                } elseif ($habit->frequency === 'weekdays') {
                    $isActive = $date->isWeekday();
                } elseif ($habit->frequency === 'weekend') {
                    $isActive = $date->isWeekend();
                }
                
                if ($isActive) {
                    $expectedDays++;
                    // Check if habit was completed on this day
                    $hasCompleted = HabitLog::where('habit_id', $habit->id)
                        ->where('user_id', $user->id)
                        ->whereDate('logged_date', $date->format('Y-m-d'))
                        ->where('completed', true)
                        ->exists();
                    
                    if ($hasCompleted) {
                        $completedDays++;
                    }
                }
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

        if ($moodStats->count() === 0 && count($habitStats) === 0) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No data available for the last 7 days to generate a summary.');
        }

        Mail::to($user->email)->send(
            new WeeklySummaryMail($user, $moodStats, $habitStats, $periodLabel)
        );

        return redirect()
            ->route('dashboard')
            ->with('status', 'A summary email for the last 7 days has been sent to your inbox.');
    }

    /**
     * Show the weekly summary dashboard with mood trends and habit completion charts.
     */
    public function weeklySummary()
    {
        $user = auth()->user();

        // Get last 7 days of mood data
        $from = now()->subDays(7)->startOfDay();
        $to = now()->endOfDay();

        // Daily mood counts for the last 7 days
        $dailyMoods = Journal::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('mood')
            ->selectRaw('DATE(created_at) as date, mood, count(*) as count')
            ->groupBy('date', 'mood')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('date');

        // Overall mood distribution
        $moodStats = Journal::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('mood')
            ->selectRaw('mood, count(*) as count')
            ->groupBy('mood')
            ->get();

        // Journal completion by day (last 7 days)
        $journalCompletionByDay = [];
        $journalStreak = 0;
        $currentStreak = 0;
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $hasJournal = Journal::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->exists();
            
            $journalCompletionByDay[$date] = $hasJournal;
            
            if ($hasJournal) {
                $currentStreak++;
                $journalStreak = max($journalStreak, $currentStreak);
            } else {
                $currentStreak = 0;
            }
        }

        // Habit completion stats with daily breakdown
        $habitStats = [];
        $habits = Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        foreach ($habits as $habit) {
            // Get daily completion for this habit
            $dailyCompletion = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $hasLog = \App\Models\HabitLog::where('habit_id', $habit->id)
                    ->where('user_id', $user->id)
                    ->whereDate('logged_date', $date)
                    ->where('completed', true)
                    ->exists();
                $dailyCompletion[$date] = $hasLog;
            }
            
            $habitStats[] = [
                'id' => $habit->id,
                'title' => $habit->title,
                'weekly_completion' => round($habit->getWeeklyCompletionPercentage(), 1),
                'current_streak' => $habit->current_streak ?? 0,
                'best_streak' => $habit->best_streak ?? 0,
                'daily_completion' => $dailyCompletion,
            ];
        }

        return view('dashboard.weekly-summary', [
            'dailyMoods' => $dailyMoods,
            'moodStats' => $moodStats,
            'habitStats' => $habitStats,
            'journalCompletionByDay' => $journalCompletionByDay,
            'journalStreak' => $journalStreak,
            'periodStart' => $from,
            'periodEnd' => $to,
        ]);
    }
}

