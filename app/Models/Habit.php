<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'frequency',          // daily, weekdays, weekend
        'goal_type',          // once, multiple_times, minutes (or older values)
        'reminder_time',
        'current_streak',
        'best_streak',
        'is_active',
        'google_event_id',    // for calendar sync
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reminder_time' => 'datetime:H:i',
    ];

    /** Get the user that owns the habit. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Get the logs for the habit. */
    public function logs()
    {
        return $this->hasMany(HabitLog::class);
    }

    /** Get today's log for the habit. */
    public function todaysLog()
    {
        return $this->hasOne(HabitLog::class)
            ->whereDate('logged_date', today());
    }

    /** Default completion percentage accessor (uses weekly percentage). */
    public function getCompletionPercentageAttribute(): float
    {
        return $this->getWeeklyCompletionPercentage();
    }

    /** Calculate completion percentage for the current week (respects frequency). */
    public function getWeeklyCompletionPercentage(): float
    {
        $startDate = now()->startOfWeek();
        $endDate   = now()->endOfWeek();

        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /** Calculate completion percentage for the current month (respects frequency). */
    public function getMonthlyCompletionPercentage(): float
    {
        $startDate = now()->startOfMonth();
        $endDate   = now()->endOfMonth();

        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /** Calculate all‑time completion percentage (respects frequency). */
    public function getAllTimeCompletionPercentage(): float
    {
        $startDate = $this->created_at->copy()->startOfDay();
        $endDate   = now()->endOfDay();

        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /**
     * Helper: calculate completion percentage for a given date range.
     * A day is considered "successful" if it has at least one completed log.
     */
    private function calculateCompletionPercentage($startDate, $endDate): float
    {
        $completedDays = $this->logs()
            ->whereBetween('logged_date', [$startDate, $endDate])
            ->where('completed', true)
            ->distinct()
            ->count('logged_date');

        $expectedDays = $this->getExpectedDaysInRange($startDate, $endDate);
        if ($expectedDays === 0) {
            return 0.0;
        }

        $percentage = ($completedDays / $expectedDays) * 100;

        return round(min($percentage, 100), 1);
    }

    /**
     * Get expected number of active days in a range based on frequency.
     *  - daily:    every calendar day
     *  - weekdays: Monday–Friday only
     *  - weekend:  Saturday & Sunday only
     */
    private function getExpectedDaysInRange($startDate, $endDate): int
    {
        $days    = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            switch ($this->frequency) {
                case 'daily':
                    $days++;
                    break;
                case 'weekdays':
                    if ($current->isWeekday()) {
                        $days++;
                    }
                    break;
                case 'weekend':
                    if ($current->isWeekend()) {
                        $days++;
                    }
                    break;
                default:
                    $days++;
            }

            $current->addDay();
        }

        return $days;
    }

    /** Expected active days from creation until now (for all‑time stats). */
    public function getExpectedDaysSinceCreation(): int
    {
        $startDate = $this->created_at->copy()->startOfDay();
        $endDate   = now()->endOfDay();

        return $this->getExpectedDaysInRange($startDate, $endDate);
    }

    /** Get consistency score (0‑100), adjusted for frequency. */
    public function getConsistencyScore(): float
    {
        $logs = $this->logs()
            ->where('completed', true)
            ->orderBy('logged_date', 'asc')
            ->get();

        if ($logs->count() < 2) {
            return 0.0;
        }

        $totalDays = 0;
        $intervals = 0;

        for ($i = 1; $i < $logs->count(); $i++) {
            $daysBetween = $logs[$i]->logged_date->diffInDays($logs[$i - 1]->logged_date);
            $totalDays  += $daysBetween;
            $intervals++;
        }

        if ($intervals === 0) {
            return 0.0;
        }

        $avgDaysBetween = $totalDays / $intervals;

        // Expected interval between completions based on frequency
        switch ($this->frequency) {
            case 'daily':
                $expectedInterval = 1;
                break;
            case 'weekdays':
                $expectedInterval = 1; // active Mon‑Fri, usually 1‑day gaps
                break;
            case 'weekend':
                $expectedInterval = 3.5; // approx. average gap between weekend days
                break;
            default:
                $expectedInterval = 5;
        }

        $deviation    = abs($avgDaysBetween - $expectedInterval);
        $maxDeviation = max($expectedInterval * 2, 1); // avoid division by zero

        $consistency = max(0, 100 - (($deviation / $maxDeviation) * 100));

        return round($consistency, 1);
    }

    /** Get progress data for charts (last N days). */
    public function getProgressData($days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate   = now()->endOfDay();

        $logs = $this->logs()
            ->whereBetween('logged_date', [$startDate, $endDate])
            ->orderBy('logged_date', 'asc')
            ->get()
            ->keyBy(fn ($log) => $log->logged_date->format('Y-m-d'));

        $data    = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $log     = $logs->get($dateKey);

            $data[] = [
                'date'      => $dateKey,
                'completed' => $log ? ($log->completed ? 1 : 0) : 0,
                'value'     => $log && $log->value_achieved ? $log->value_achieved : 0,
            ];

            $current->addDay();
        }

        return $data;
    }

    /**
     * Recalculate and update streaks based on all logs.
     * NOTE: currently uses calendar days; can be made frequency‑aware later.
     */
    public function recalculateStreaks(): void
    {
        $allLogs = $this->logs()
            ->orderBy('logged_date', 'desc')
            ->get();

        $completedLogs = $allLogs->where('completed', true);

        if ($completedLogs->isEmpty()) {
            $this->update(['current_streak' => 0]);
            return;
        }

        // Current streak: count consecutive days up to today
        $todayLog = $allLogs->firstWhere('logged_date', today());
        if (!$todayLog || !$todayLog->completed) {
            $currentStreak = 0;
        } else {
            $currentStreak = 0;
            $expectedDate  = today();

            foreach ($completedLogs as $log) {
                $logDate = $log->logged_date;

                if ($logDate->isSameDay($expectedDate) ||
                    ($logDate->isBefore($expectedDate) && $logDate->diffInDays($expectedDate) <= 1)) {
                    $currentStreak++;
                    $expectedDate = $logDate->copy()->subDay();
                } else {
                    break;
                }
            }
        }

        // Best streak over all history, allowing a 1‑day gap
        $bestStreak = 0;
        $tempStreak = 0;
        $prevDate   = null;

        foreach ($completedLogs->reverse() as $log) {
            $logDate = $log->logged_date;

            if ($prevDate === null) {
                $tempStreak = 1;
            } elseif ($logDate->diffInDays($prevDate) <= 2) {
                $tempStreak++;
            } else {
                $bestStreak = max($bestStreak, $tempStreak);
                $tempStreak = 1;
            }

            $prevDate = $logDate;
        }

        $bestStreak = max($bestStreak, $tempStreak);

        $this->update([
            'current_streak' => $currentStreak,
            'best_streak'    => max($this->best_streak, $bestStreak),
        ]);
    }
}
