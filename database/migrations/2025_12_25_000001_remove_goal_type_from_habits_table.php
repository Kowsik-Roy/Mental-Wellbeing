<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            // Remove goal_type column as it's no longer functionally necessary
            // We can infer the type from value_achieved in habit_logs
            if (Schema::hasColumn('habits', 'goal_type')) {
                $table->dropColumn('goal_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            // Add goal_type back if needed
            if (!Schema::hasColumn('habits', 'goal_type')) {
                $table->enum('goal_type', ['once', 'multiple_times', 'minutes'])->default('once')->after('frequency');
            }
        });
    }
};
