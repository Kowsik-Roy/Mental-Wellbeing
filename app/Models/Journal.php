<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'mood', // Add mood to fillable
    ];

    // Define available moods
    public const MOODS = [
        'happy' => '😊 Happy',
        'sad' => '😢 Sad',
        'excited' => '🎉 Excited',
        'angry' => '😠 Angry',
        'anxious' => '😰 Anxious',
        'calm' => '😌 Calm',
        'tired' => '😴 Tired',
        'neutral' => '😐 Neutral',
        'grateful' => '🙏 Grateful',
        'inspired' => '✨ Inspired',
        'confused' => '😕 Confused',
        'proud' => '🦸 Proud',
        'loved' => '❤️ Loved',
        'nostalgic' => '📷 Nostalgic',
        'hopeful' => '🌈 Hopeful',
    ];

    // Get mood with emoji
    public function getMoodWithEmojiAttribute()
    {
        if (!$this->mood) {
            return 'No mood selected';
        }
        
        $moods = self::MOODS;
        return $moods[$this->mood] ?? $this->mood;
    }

    // Get mood emoji only
    public function getMoodEmojiAttribute()
    {
        if (!$this->mood) {
            return '';
        }
        
        $moods = self::MOODS;
        $moodText = $moods[$this->mood] ?? $this->mood;
        
        // Extract emoji from text (first character before space)
        return explode(' ', $moodText)[0] ?? '';
    }

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}