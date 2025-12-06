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
            ->whereDate('logged_date', today());
    }

    /**
     * Calculate completion percentage for the current period.
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->goal_type === 'boolean') {
            $completed = $this->logs()
                ->whereDate('logged_date', '>=', now()->startOfWeek())
                ->where('completed', true)
                ->count();
            
            $days = $this->frequency === 'daily' ? 7 : 1;
            return ($completed / $days) * 100;
        }
        
        return 0;
    }
}