<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            // Remove the target_value column if it exists
            if (Schema::hasColumn('habits', 'target_value')) {
                $table->dropColumn('target_value');
            }
            
            // Update the goal_type enum values
            $table->enum('goal_type', ['once', 'multiple_times', 'minutes'])->default('once')->change();
            
            // Update the frequency enum values  
            $table->enum('frequency', ['daily', 'weekdays', 'weekend'])->default('daily')->change();
        });
    }

    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->integer('target_value')->nullable()->after('goal_type');
            $table->enum('goal_type', ['times', 'minutes', 'boolean'])->default('boolean')->change();
            $table->enum('frequency', ['daily', 'weekly', 'weekdays', 'custom'])->default('daily')->change();
        });
    }
};