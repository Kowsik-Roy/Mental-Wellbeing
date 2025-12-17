<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Services\HuggingFaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WellnessController extends Controller
{
    /**
     * Show the wellness recommendations page.
     */
    public function index()
    {
        return view('wellness.index');
    }

    /**
     * Generate wellness recommendations based on user data.
     */
    public function generate(Request $request)
    {
        $user = Auth::user();
        
        // Find the first activity date (first habit log or journal entry)
        $firstHabitLogDate = HabitLog::whereHas('habit', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('logged_date', 'asc')->value('logged_date');
        
        $firstJournalDate = Journal::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->value('created_at');
        
        if (!$firstHabitLogDate && !$firstJournalDate) {
            return response()->json([
                'success' => false,
                'message' => 'You need to have at least one habit log or journal entry to generate recommendations.'
            ], 400);
        }
        
        // Determine the earliest activity date
        $firstActivityDate = null;
        if ($firstHabitLogDate && $firstJournalDate) {
            $firstHabitLogCarbon = Carbon::parse($firstHabitLogDate);
            $firstJournalCarbon = Carbon::parse($firstJournalDate);
            $firstActivityDate = $firstHabitLogCarbon->lt($firstJournalCarbon) ? $firstHabitLogCarbon : $firstJournalCarbon;
        } elseif ($firstHabitLogDate) {
            $firstActivityDate = Carbon::parse($firstHabitLogDate);
        } else {
            $firstActivityDate = Carbon::parse($firstJournalDate);
        }
        
        // Calculate period based on first activity
        $now = Carbon::now();
        $daysSinceFirstActivity = $firstActivityDate->diffInDays($now);
        
        if ($daysSinceFirstActivity < 7) {
            // If less than 7 days since first activity, use all available data
            $periodStart = $firstActivityDate->copy()->startOfDay();
            $periodEnd = $now->copy()->endOfDay();
        } else {
            // If 7+ days, use the last 7 days from today
            $periodEnd = $now->copy()->endOfDay();
            $periodStart = $now->copy()->subDays(6)->startOfDay();
        }
        
        // Calculate mood trend for the period
        $periodMoods = Journal::where('user_id', $user->id)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->whereNotNull('mood')
            ->pluck('mood')
            ->toArray();
        
        $moodTrend = $this->calculateMoodTrend($periodMoods);
        
        // Calculate habit summary for the period
        $habits = Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        // Calculate actual days in period (for display)
        $actualDays = (int) round($periodStart->diffInDays($periodEnd)) + 1;
        
        $habitSummary = $this->calculateHabitSummary($habits, $periodStart, $periodEnd, $actualDays);
        
        // Generate AI recommendation
        try {
            $huggingFaceService = new HuggingFaceService();
            $recommendation = $huggingFaceService->generateWellnessRecommendation(
                $moodTrend,
                $habitSummary
            );
            
            return response()->json([
                'success' => true,
                'recommendation' => $recommendation,
                'data' => [
                    'mood_trend' => $moodTrend,
                    'habit_summary' => $habitSummary,
                    'period_start' => $periodStart->format('Y-m-d'),
                    'period_end' => $periodEnd->format('Y-m-d'),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Wellness recommendation generation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recommendation. Please try again later.'
            ], 500);
        }
    }
    
    /**
     * Calculate mood trend description.
     */
    private function calculateMoodTrend(array $moods): string
    {
        if (empty($moods)) {
            return 'No mood entries recorded';
        }
        
        $moodCounts = array_count_values($moods);
        arsort($moodCounts);
        
        $topMoods = array_slice($moodCounts, 0, 3, true);
        $moodLabels = Journal::MOODS;
        
        $trendParts = [];
        foreach ($topMoods as $mood => $count) {
            $label = $moodLabels[$mood] ?? $mood;
            $percentage = round(($count / count($moods)) * 100);
            $trendParts[] = "{$label} ({$percentage}%)";
        }
        
        return implode(', ', $trendParts);
    }
    
    /**
     * Calculate habit completion summary for the period.
     */
    private function calculateHabitSummary($habits, $periodStart, $periodEnd, int $actualDays): string
    {
        if ($habits->isEmpty()) {
            return 'No active habits';
        }
        
        $totalCompletions = 0;
        $totalExpected = 0;
        
        foreach ($habits as $habit) {
            $logs = HabitLog::where('habit_id', $habit->id)
                ->whereBetween('logged_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->where('completed', true)
                ->distinct('logged_date')
                ->count('logged_date');
            
            $expected = $this->getExpectedDaysInRange($habit, $periodStart, $periodEnd);
            $totalExpected += $expected;
            $totalCompletions += $logs;
        }
        
        $overallPercentage = $totalExpected > 0 ? round(($totalCompletions / $totalExpected) * 100) : 0;
        
        // Create a more meaningful summary
        $habitCount = $habits->count();
        $avgCompletions = $habitCount > 0 ? round($totalCompletions / $habitCount, 1) : 0;
        $avgExpected = $habitCount > 0 ? round($totalExpected / $habitCount, 1) : 0;
        
        // Use appropriate phrasing based on actual days
        if ($actualDays < 7) {
            $periodPhrase = "Over the last {$actualDays} day" . ($actualDays > 1 ? 's' : '');
        } else {
            $periodPhrase = "Over the last 7 days";
        }
        
        return "{$periodPhrase}: {$habitCount} active habit" . ($habitCount > 1 ? 's' : '') . " with an average of {$avgCompletions}/{$avgExpected} completions per habit ({$overallPercentage}% overall completion rate).";
    }
    
    /**
     * Get expected days in range for a habit based on frequency.
     */
    private function getExpectedDaysInRange($habit, $startDate, $endDate): int
    {
        $days = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            if ($habit->frequency === 'daily') {
                $days++;
            } elseif ($habit->frequency === 'weekdays' && $current->isWeekday()) {
                $days++;
            } elseif ($habit->frequency === 'weekend' && $current->isWeekend()) {
                $days++;
            }
            $current->addDay();
        }
        
        return $days;
    }
    
}
