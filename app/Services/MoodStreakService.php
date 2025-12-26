<?php

namespace App\Services;

use App\Models\MoodLog;
use Carbon\Carbon;

class MoodStreakService
{
    // Change these mood strings to match your dropdown options
    private array $negativeMoods = ['Sad', 'Very Sad', 'Depressed', 'Anxious', 'Stressed'];

    public function needsAlert(int $userId, int $days = 3): bool
    {
        $logs = MoodLog::where('user_id', $userId)
            ->whereDate('log_date', '>=', Carbon::today()->subDays($days - 1))
            ->orderBy('log_date', 'desc')
            ->get();

        if ($logs->count() < $days) return false;

        $negCount = $logs->filter(fn($l) => in_array($l->evening_mood, $this->negativeMoods))->count();
        $inactiveCount = $logs->filter(fn($l) => $l->was_active === false)->count();

        return $negCount >= $days && $inactiveCount >= 2;
    }
}
