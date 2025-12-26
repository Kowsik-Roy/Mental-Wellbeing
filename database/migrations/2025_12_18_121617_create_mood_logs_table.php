<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mood_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');

            // Morning check-in
            $table->string('morning_mood')->nullable();          // e.g. "happy", "sad"
            $table->text('morning_plan')->nullable();           // upcoming activities

            // End of day check-in
            $table->string('evening_mood')->nullable();          // mood at end of day
            $table->text('day_summary')->nullable();            // how day actually went
            $table->boolean('was_active')->nullable();           // true/false

            // Alert flow (future feature)
            $table->boolean('alert_suggested')->default(false);
            $table->boolean('alert_confirmed')->default(false);
            $table->timestamp('alert_sent_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mood_logs');
    }
};
