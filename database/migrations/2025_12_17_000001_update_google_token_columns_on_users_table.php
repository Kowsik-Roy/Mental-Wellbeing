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
        Schema::table('users', function (Blueprint $table) {
            // Drop the old short varchar columns (they are currently empty)
            if (Schema::hasColumn('users', 'google_token')) {
                $table->dropColumn('google_token');
            }
            if (Schema::hasColumn('users', 'google_refresh_token')) {
                $table->dropColumn('google_refresh_token');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Re-add as text so we can store full JSON tokens safely
            $table->text('google_token')->nullable()->after('password');
            $table->text('google_refresh_token')->nullable()->after('google_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'google_token')) {
                $table->dropColumn('google_token');
            }
            if (Schema::hasColumn('users', 'google_refresh_token')) {
                $table->dropColumn('google_refresh_token');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Restore original short string columns
            $table->string('google_token')->nullable()->after('password');
            $table->string('google_refresh_token')->nullable()->after('google_token');
        });
    }
};
