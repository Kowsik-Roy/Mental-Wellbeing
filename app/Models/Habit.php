<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'frequency',
        'goal_type',
        'target_value',
        'current_streak',
        'best_streak',
        'is_active',
        'reminder_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_value' => 'integer',
        'current_streak' => 'integer',
        'best_streak' => 'integer',
    ];

    /**
     * Get the user that owns the habit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the logs for the habit.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    /**
     * Get today's log for the habit.
     */
    public function todaysLog()
    {
        return $this->hasOne(HabitLog::class)
            ->whereDate('logged_date', today())
            ->where('user_id', $this->user_id);
    }

    /**
     * Calculate completion percentage for the current period.
     */
    public function getCompletionPercentageAttribute(): float
    {
        return $this->getWeeklyCompletionPercentage();
    }

    /**
     * Calculate completion percentage for the current week.
     */
    public function getWeeklyCompletionPercentage(): float
    {
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();
        
        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /**
     * Calculate completion percentage for the current month.
     */
    public function getMonthlyCompletionPercentage(): float
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        
        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /**
     * Calculate all-time completion percentage.
     */
    public function getAllTimeCompletionPercentage(): float
    {
        $startDate = $this->created_at->startOfDay();
        $endDate = now()->endOfDay();
        
        return $this->calculateCompletionPercentage($startDate, $endDate);
    }

    /**
     * Calculate completion percentage for a date range.
     */
    private function calculateCompletionPercentage($startDate, $endDate): float
    {
        if ($this->goal_type === 'boolean') {
            $completed = $this->logs()
                ->whereBetween('logged_date', [$startDate, $endDate])
                ->where('completed', true)
                ->count();
            
            // Calculate expected days based on frequency
            $expectedDays = $this->getExpectedDaysInRange($startDate, $endDate);
            
            if ($expectedDays === 0) {
                return 0;
            }
            
            return min(($completed / $expectedDays) * 100, 100);
        } elseif ($this->goal_type === 'times' || $this->goal_type === 'minutes') {
            $totalAchieved = $this->logs()
                ->whereBetween('logged_date', [$startDate, $endDate])
                ->where('completed', true)
                ->sum('value_achieved') ?? 0;
            
            $expectedDays = $this->getExpectedDaysInRange($startDate, $endDate);
            $targetTotal = $expectedDays * $this->target_value;
            
            if ($targetTotal === 0) {
                return 0;
            }
            
            return min(($totalAchieved / $targetTotal) * 100, 100);
        }
        
        return 0;
    }

    /**
     * Get expected number of days in a range based on frequency.
     */
    private function getExpectedDaysInRange($startDate, $endDate): int
    {
        $days = 0;
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
                case 'weekly':
                    // For weekly, count only if it's the same day of week as created
                    if ($current->dayOfWeek === $this->created_at->dayOfWeek) {
                        $days++;
                    }
                    break;
            }
            $current->addDay();
        }
        
        return max($days, 1); // At least 1 to avoid division by zero
    }

    /**
     * Get consistency score (0-100) based on how consistent the habit is over time.
     */
    public function getConsistencyScore(): float
    {
        $logs = $this->logs()
            ->where('completed', true)
            ->orderBy('logged_date', 'asc')
            ->get();
        
        if ($logs->count() < 2) {
            return 0;
        }
        
        // Calculate average days between completions
        $totalDays = 0;
        $intervals = 0;
        
        for ($i = 1; $i < $logs->count(); $i++) {
            $daysBetween = $logs[$i]->logged_date->diffInDays($logs[$i - 1]->logged_date);
            $totalDays += $daysBetween;
            $intervals++;
        }
        
        if ($intervals === 0) {
            return 0;
        }
        
        $avgDaysBetween = $totalDays / $intervals;
        
        // For daily habits, consistency is better if avgDaysBetween is close to 1
        // For weekly habits, consistency is better if avgDaysBetween is close to 7
        $expectedInterval = $this->frequency === 'daily' ? 1 : ($this->frequency === 'weekly' ? 7 : 5);
        
        // Calculate consistency: closer to expected interval = higher score
        $deviation = abs($avgDaysBetween - $expectedInterval);
        $maxDeviation = $expectedInterval * 2; // Allow up to 2x the expected interval
        
        $consistency = max(0, 100 - (($deviation / $maxDeviation) * 100));
        
        return round($consistency, 1);
    }

    /**
     * Get progress data for charts (last 30 days).
     */
    public function getProgressData($days = 30): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();
        
        $logs = $this->logs()
            ->whereBetween('logged_date', [$startDate, $endDate])
            ->orderBy('logged_date', 'asc')
            ->get()
            ->keyBy(function ($log) {
                return $log->logged_date->format('Y-m-d');
            });
        
        $data = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dateKey = $current->format('Y-m-d');
            $log = $logs->get($dateKey);
            
            $data[] = [
                'date' => $dateKey,
                'completed' => $log ? ($log->completed ? 1 : 0) : 0,
                'value' => $log && $log->value_achieved ? $log->value_achieved : 0,
            ];
            
            $current->addDay();
        }
        
        return $data;
    }

    /**
     * Recalculate and update streaks based on all logs.
     */
    public function recalculateStreaks(): void
    {
        // Get all logs (both completed and not completed) ordered by date
        $allLogs = $this->logs()
            ->orderBy('logged_date', 'desc')
            ->get();
        
        $completedLogs = $allLogs->where('completed', true);
        
        if ($completedLogs->isEmpty()) {
            $this->update(['current_streak' => 0]);
            return;
        }
        
        // Check if today is completed
        $todayLog = $allLogs->firstWhere('logged_date', today());
        if (!$todayLog || !$todayLog->completed) {
            // If today is not completed, current streak is 0
            $currentStreak = 0;
        } else {
            // Calculate current streak from today backwards
            $currentStreak = 0;
            $expectedDate = today();
            
            foreach ($completedLogs as $log) {
                $logDate = $log->logged_date;
                
                // Check if this log is consecutive (within 1 day of expected date)
                if ($logDate->isSameDay($expectedDate) || 
                    ($logDate->isBefore($expectedDate) && $logDate->diffInDays($expectedDate) <= 1)) {
                    $currentStreak++;
                    $expectedDate = $logDate->copy()->subDay();
                } else {
                    break;
                }
            }
        }
        
        // Calculate best streak from all completed logs
        $bestStreak = 0;
        $tempStreak = 0;
        $prevDate = null;
        
        foreach ($completedLogs->reverse() as $log) {
            $logDate = $log->logged_date;
            
            if ($prevDate === null) {
                $tempStreak = 1;
            } elseif ($logDate->diffInDays($prevDate) <= 2) {
                // Allow 1 day gap for flexibility
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
            'best_streak' => max($this->best_streak, $bestStreak),
        ]);
    }
}