<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Journal;
use App\Models\Habit;
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

        // Use last 7 days (rolling 7 days from today, including today)
        $now = \Carbon\Carbon::now('Asia/Dhaka');
        $from = $now->copy()->subDays(6)->startOfDay(); // Last 7 days including today
        $to = $now->copy()->endOfDay();
        $periodLabel = $from->format('M d, Y') . ' - ' . $to->format('M d, Y');

        // Mood stats for the last 7 days
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

        // Habit stats for the last 7 days
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

        if ($moodStats->count() === 0 && count($habitStats) === 0) {
            return redirect()
                ->route('dashboard.weekly-summary')
                ->with('error', 'No data available for the last 7 days to generate a summary.');
        }

        Mail::to($user->email)->send(
            new WeeklySummaryMail($user, $moodStats, $habitStats, $periodLabel)
        );

        return redirect()
            ->route('dashboard.weekly-summary')
            ->with('status', 'A weekly summary email has been sent to your inbox.');
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

