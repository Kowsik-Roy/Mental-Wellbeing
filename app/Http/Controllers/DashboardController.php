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

        // Use the same period as the scheduled weekly summary: previous full week
        $from = now()->subWeek()->startOfWeek();
        $to = now()->subWeek()->endOfWeek();
        $periodLabel = $from->format('M d, Y') . ' - ' . $to->format('M d, Y');

        // Mood stats for the period
        $moodStats = Journal::where('user_id', $user->id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('mood')
            ->selectRaw('mood, count(*) as count')
            ->groupBy('mood')
            ->get();

        // Habit stats (re-using the same structure as the console command)
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

        if ($moodStats->count() === 0 && count($habitStats) === 0) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'No data available for the last week to generate a summary.');
        }

        Mail::to($user->email)->send(
            new WeeklySummaryMail($user, $moodStats, $habitStats, $periodLabel)
        );

        return redirect()
            ->route('dashboard')
            ->with('status', 'A weekly summary email has been sent to your inbox.');
    }
}

