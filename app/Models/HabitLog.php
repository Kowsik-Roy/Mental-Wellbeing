<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'habit_id',
        'user_id',
        'completed',
        'value_achieved',
        'notes',
        'logged_date',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'logged_date' => 'date',
        'value_achieved' => 'integer',
    ];

    /**
     * Get the habit that owns the log.
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}