<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mood_logs', function (Blueprint $table) {

            // Only add columns that do NOT already exist
            if (!Schema::hasColumn('mood_logs', 'planned_activities')) {
                $table->text('planned_activities')->nullable();
            }

            if (!Schema::hasColumn('mood_logs', 'evening_mood')) {
                $table->string('evening_mood')->nullable();
            }

            if (!Schema::hasColumn('mood_logs', 'day_summary')) {
                $table->text('day_summary')->nullable();
            }

            if (!Schema::hasColumn('mood_logs', 'was_active')) {
                $table->boolean('was_active')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('mood_logs', function (Blueprint $table) {
            // leave empty for safety
        });
    }
};
