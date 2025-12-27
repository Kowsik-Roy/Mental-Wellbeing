<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_key',
        'badge_name',
        'earned_at',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
    ];

    /**
     * Get the user that earned this badge.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Badge definitions
     */
    public static function getBadgeDefinitions(): array
    {
        return [
            ['days' => 3,  'key' => 'streak_3',  'name' => '🌱 Seedling (3-day streak)'],
            ['days' => 7,  'key' => 'streak_7',  'name' => '🔥 Flame (7-day streak)'],
            ['days' => 14, 'key' => 'streak_14', 'name' => '🌼 Bloom (14-day streak)'],
            ['days' => 30, 'key' => 'streak_30', 'name' => '🏆 Champion (30-day streak)'],
        ];
    }
}

