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
    ];

    // Relationship: Each journal entry belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
