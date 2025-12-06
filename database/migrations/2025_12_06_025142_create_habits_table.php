<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('frequency', ['daily', 'weekly', 'weekdays', 'custom'])->default('daily');
            $table->enum('goal_type', ['times', 'minutes', 'boolean'])->default('boolean');
            $table->integer('target_value')->default(1);
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->boolean('is_active')->default(true);
            $table->time('reminder_time')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};