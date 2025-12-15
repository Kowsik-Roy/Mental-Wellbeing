<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, convert existing data to new values
        DB::table('habits')->where('goal_type', 'boolean')->update(['goal_type' => 'once']);
        DB::table('habits')->where('goal_type', 'times')->update(['goal_type' => 'multiple_times']);
        // 'minutes' stays the same
        
        // Then update the column definition
        Schema::table('habits', function (Blueprint $table) {
            $table->enum('goal_type', ['once', 'multiple_times', 'minutes'])->default('once')->change();
            $table->enum('frequency', ['daily', 'weekdays', 'weekend'])->default('daily')->change();
            
            // Remove target_value if it exists
            if (Schema::hasColumn('habits', 'target_value')) {
                $table->dropColumn('target_value');
            }
        });
    }

    public function down(): void
    {
        // Revert the changes if needed
        Schema::table('habits', function (Blueprint $table) {
            // Add target_value back if needed
            if (!Schema::hasColumn('habits', 'target_value')) {
                $table->integer('target_value')->nullable()->after('goal_type');
            }
            
            // Convert data back
            DB::table('habits')->where('goal_type', 'once')->update(['goal_type' => 'boolean']);
            DB::table('habits')->where('goal_type', 'multiple_times')->update(['goal_type' => 'times']);
            // 'minutes' stays 'minutes'
            
            $table->enum('goal_type', ['boolean', 'times', 'minutes'])->default('boolean')->change();
            $table->enum('frequency', ['daily', 'weekly', 'weekdays', 'custom'])->default('daily')->change();
        });
    }
};