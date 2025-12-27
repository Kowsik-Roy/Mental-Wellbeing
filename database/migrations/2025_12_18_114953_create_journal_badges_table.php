<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_badges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Badge identifier (ex: streak_3, streak_7, streak_30)
            $table->string('badge_key');

            // Optional display text
            $table->string('badge_name');

            // When user earned it
            $table->timestamp('earned_at')->useCurrent();

            $table->timestamps();

            $table->unique(['user_id', 'badge_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_badges');
    }
};
