<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoodLog extends Model
{
    protected $fillable = [
        'user_id',
        'log_date',
        'morning_mood',
        'planned_activities',
        'evening_mood',
        'day_summary',
        'was_active',
        'alert_suggested',
        'alert_confirmed',
        'alert_sent_at',
    ];

    protected $casts = [
        'log_date' => 'date',
        'was_active' => 'boolean',
        'alert_suggested' => 'boolean',
        'alert_confirmed' => 'boolean',
        'alert_sent_at' => 'datetime',
    ];
}
