<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing table
        Schema::dropIfExists('habit_logs');
        Schema::dropIfExists('habits');
        
        // Recreate habits table with correct structure
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('frequency', ['daily', 'weekdays', 'weekend'])->default('daily');
            $table->enum('goal_type', ['once', 'multiple_times', 'minutes'])->default('once');
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->boolean('is_active')->default(true);
            $table->time('reminder_time')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });
        
        // Recreate habit_logs table
        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->integer('value_achieved')->nullable();
            $table->text('notes')->nullable();
            $table->date('logged_date');
            $table->timestamps();
            
            $table->unique(['habit_id', 'logged_date']);
            $table->index(['user_id', 'logged_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_logs');
        Schema::dropIfExists('habits');
    }
};