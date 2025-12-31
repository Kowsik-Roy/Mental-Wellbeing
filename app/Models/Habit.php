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

    /**
     * Infer goal_type from logs for display purposes.
     * Returns 'once', 'multiple_times', or 'minutes' based on value_achieved in logs.
     */
    public function getGoalTypeAttribute(): string
    {
        // Check if any log has value_achieved set
        $hasValueAchieved = $this->logs()
            ->whereNotNull('value_achieved')
            ->where('value_achieved', '>', 0)
            ->exists();
        
        if (!$hasValueAchieved) {
            return 'once';
        }
        
        // For now, default to 'multiple_times' if value_achieved exists
        // In the future, we could add a field to distinguish minutes vs times
        // For simplicity, we'll use 'multiple_times' as the default
        return 'multiple_times';
    }

    /** Default completion percentage accessor (uses weekly percentage). */
    public function getCompletionPercentageAttribute(): float
    {
        return $this->getWeeklyCompletionPercentage();
    }

    /** Calculate completion percentage for the current week (respects frequency). */
    public function getWeeklyCompletionPercentage(): float
    {
        // Week starts on Monday and ends on Sunday
        $now = \Carbon\Carbon::now('Asia/Dhaka');
        $startDate = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $endDate   = $now->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /** Calculate completion percentage for a custom date range (respects frequency). */
    public function getCompletionPercentageForRange($startDate, $endDate): float
    {
        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /** Calculate completion percentage for the current month (respects frequency). */
    public function getMonthlyCompletionPercentage(): float
    {
        // Month starts on the 1st day and ends on the last day
        $now = \Carbon\Carbon::now('Asia/Dhaka');
        $startDate = $now->copy()->startOfMonth(); // First day of month at 00:00:00
        $endDate   = $now->copy()->endOfMonth();   // Last day of month at 23:59:59

        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /** Calculate all‑time completion percentage (respects frequency). */
    public function getAllTimeCompletionPercentage(): float
    {
        $startDate = $this->created_at->copy()
            ->setTimezone('Asia/Dhaka')
            ->startOfDay();
        $endDate = \Carbon\Carbon::now('Asia/Dhaka')->endOfDay();

        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /**
     * Helper: calculate completion percentage for a given date range.
     * A day is considered "successful" if it has at least one completed log on an active day.
     */
    private function calculateCompletionPercentage($startDate, $endDate): float
    {
        // Ensure dates are in Asia/Dhaka timezone
        $startDate = $startDate instanceof \Carbon\Carbon 
            ? $startDate->copy()->setTimezone('Asia/Dhaka')->startOfDay()
            : \Carbon\Carbon::parse($startDate)->setTimezone('Asia/Dhaka')->startOfDay();
        $endDate = $endDate instanceof \Carbon\Carbon 
            ? $endDate->copy()->setTimezone('Asia/Dhaka')->endOfDay()
            : \Carbon\Carbon::parse($endDate)->setTimezone('Asia/Dhaka')->endOfDay();

        // Get all completed logs in the range
        $allLogs = $this->logs()
            ->whereBetween('logged_date', [$startDate, $endDate])
            ->where('completed', true)
            ->get();

        // Filter to only count logs on active days for this habit's frequency
        $completedDays = 0;
        $seenDates = [];
        
        foreach ($allLogs as $log) {
            $logDate = $log->logged_date instanceof \Carbon\Carbon 
                ? $log->logged_date->copy()
                : \Carbon\Carbon::parse($log->logged_date);
            $logDate->setTimezone('Asia/Dhaka')->startOfDay();
            
            // Only count if this log is on an active day and we haven't counted this date yet
            if ($this->isActiveOnDate($logDate)) {
                $dateKey = $logDate->format('Y-m-d');
                if (!in_array($dateKey, $seenDates)) {
                    $completedDays++;
                    $seenDates[] = $dateKey;
                }
            }
        }

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
        // Normalize start date to start of day for counting
        $startDate = $startDate instanceof \Carbon\Carbon 
            ? $startDate->copy()->setTimezone('Asia/Dhaka')->startOfDay()
            : \Carbon\Carbon::parse($startDate)->setTimezone('Asia/Dhaka')->startOfDay();
        
        // Normalize end date to start of day for comparison (we count days, not times)
        // This ensures we count the end date as a full day
        $endDate = $endDate instanceof \Carbon\Carbon 
            ? $endDate->copy()->setTimezone('Asia/Dhaka')->startOfDay()
            : \Carbon\Carbon::parse($endDate)->setTimezone('Asia/Dhaka')->startOfDay();

        $days    = 0;
        $current = $startDate->copy();

        // Loop through each day from start to end (inclusive)
        while ($current->lte($endDate)) {
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
        $startDate = $this->created_at->copy()
            ->setTimezone('Asia/Dhaka')
            ->startOfDay();
        $endDate = \Carbon\Carbon::now('Asia/Dhaka')->endOfDay();

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
     * Recalculate and update streaks based on frequency.
     * 
     * - Daily: consecutive calendar days
     * - Weekdays: consecutive weekdays (Mon-Fri), weekends don't break streak
     * - Weekend: consecutive weekends (Sat-Sun), weekdays don't break streak
     */
    public function recalculateStreaks(): void
    {
        $completedLogs = $this->logs()
            ->where('completed', true)
            ->orderBy('logged_date', 'asc')
            ->get();

        if ($completedLogs->isEmpty()) {
            $this->update([
                'current_streak' => 0,
                'best_streak' => 0,
            ]);
            return;
        }

        // Calculate current streak (from today backwards)
        $currentStreak = $this->calculateCurrentStreak($completedLogs);
        
        // Calculate best streak (over all history)
        $bestStreak = $this->calculateBestStreak($completedLogs);

        $this->update([
            'current_streak' => $currentStreak,
            'best_streak' => $bestStreak, // Always use recalculated value
        ]);
    }

    /**
     * Calculate current streak from today backwards.
     */
    private function calculateCurrentStreak($completedLogs): int
    {
        $now = \Carbon\Carbon::now('Asia/Dhaka');
        $streak = 0;
        $expectedDate = $now->copy()->startOfDay();

        // Check if today should be completed based on frequency
        if (!$this->isActiveOnDate($expectedDate)) {
            // If today is not an active day, start from the last active day
            $expectedDate = $this->getPreviousActiveDate($expectedDate);
        }

        // Work backwards from expected date
        while ($expectedDate && $expectedDate->lte($now)) {
            $log = $completedLogs->firstWhere(function($log) use ($expectedDate) {
                $logDate = $log->logged_date instanceof \Carbon\Carbon 
                    ? $log->logged_date->copy()
                    : \Carbon\Carbon::parse($log->logged_date);
                $logDate->setTimezone('Asia/Dhaka')->startOfDay();
                return $logDate->isSameDay($expectedDate);
            });

            if ($log) {
                $streak++;
                $expectedDate = $this->getPreviousActiveDate($expectedDate);
            } else {
                // Missing completion breaks the streak
                break;
            }
        }

        return $streak;
    }

    /**
     * Calculate best streak over all history.
     */
    private function calculateBestStreak($completedLogs): int
    {
        if ($completedLogs->isEmpty()) {
            return 0;
        }

        $bestStreak = 0;
        $currentStreak = 0;
        $lastActiveDate = null;

        foreach ($completedLogs as $log) {
            // logged_date is already a Carbon instance (cast as 'date')
            $logDate = $log->logged_date instanceof \Carbon\Carbon 
                ? $log->logged_date->copy()
                : \Carbon\Carbon::parse($log->logged_date);
            $logDate->setTimezone('Asia/Dhaka')->startOfDay();

            // Only count logs on active days for this frequency
            if (!$this->isActiveOnDate($logDate)) {
                continue;
            }

            if ($lastActiveDate === null) {
                // First active day
                $currentStreak = 1;
                $lastActiveDate = $logDate;
            } else {
                // Check if this is consecutive
                $expectedDate = $this->getNextActiveDate($lastActiveDate);
                
                // Ensure both dates are in same timezone for comparison
                if ($expectedDate) {
                    $expectedDate->setTimezone('Asia/Dhaka')->startOfDay();
                }
                
                if ($expectedDate && $logDate->isSameDay($expectedDate)) {
                    // Consecutive active day
                    $currentStreak++;
                    $lastActiveDate = $logDate;
                } else {
                    // Streak broken - save current streak and start new one
                    $bestStreak = max($bestStreak, $currentStreak);
                    $currentStreak = 1;
                    $lastActiveDate = $logDate;
                }
            }
        }

        // Check final streak (don't forget the last one)
        $bestStreak = max($bestStreak, $currentStreak);

        return $bestStreak;
    }

    /**
     * Check if habit is active today based on frequency.
     * Public method for use in views and controllers.
     */
    public function isActiveToday(): bool
    {
        $today = \Carbon\Carbon::now('Asia/Dhaka')->startOfDay();
        return $this->isActiveOnDate($today);
    }

    /**
     * Check if habit is active on a given date based on frequency.
     * Public method for use in controllers.
     */
    public function isActiveOnDate(\Carbon\Carbon $date): bool
    {
        switch ($this->frequency) {
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
     * Get the previous active date before the given date.
     */
    private function getPreviousActiveDate(\Carbon\Carbon $date): ?\Carbon\Carbon
    {
        $date = $date->copy()->setTimezone('Asia/Dhaka')->startOfDay();
        
        switch ($this->frequency) {
            case 'daily':
                return $date->copy()->subDay();
            case 'weekdays':
                // Go back to previous weekday
                $previous = $date->copy()->subDay();
                while ($previous->isWeekend()) {
                    $previous->subDay();
                }
                return $previous;
            case 'weekend':
                // Go back to previous weekend day
                $previous = $date->copy()->subDay();
                if ($previous->isWeekend()) {
                    // If previous day is Saturday, we want last Sunday
                    if ($previous->isSaturday()) {
                        return $previous->copy()->subDay(); // Last Sunday
                    }
                    // If previous day is Sunday, we want last Saturday
                    return $previous->copy()->subDays(6); // Last Saturday
                }
                // If it's a weekday, go back to previous Sunday
                $daysToSubtract = $previous->dayOfWeek + 1; // 0=Sunday
                return $previous->copy()->subDays($daysToSubtract);
            default:
                return $date->copy()->subDay();
        }
    }

    /**
     * Get the next active date after the given date.
     */
    private function getNextActiveDate(\Carbon\Carbon $date): ?\Carbon\Carbon
    {
        $date = $date->copy()->setTimezone('Asia/Dhaka')->startOfDay();
        
        switch ($this->frequency) {
            case 'daily':
                return $date->copy()->addDay();
            case 'weekdays':
                // Go forward to next weekday
                $next = $date->copy()->addDay();
                while ($next->isWeekend()) {
                    $next->addDay();
                }
                return $next;
            case 'weekend':
                // Go forward to next weekend day
                $next = $date->copy()->addDay();
                if ($next->isWeekend()) {
                    // If next day is Saturday, we want next Sunday (tomorrow)
                    if ($next->isSaturday()) {
                        return $next; // Next Sunday (tomorrow)
                    }
                    // If next day is Sunday, we want next Saturday
                    return $next->copy()->addDays(6); // Next Saturday
                }
                // If it's a weekday, go forward to next Saturday
                $daysToAdd = 6 - $next->dayOfWeek; // 0=Sunday, 6=Saturday
                return $next->copy()->addDays($daysToAdd);
            default:
                return $date->copy()->addDay();
        }
    }
}
