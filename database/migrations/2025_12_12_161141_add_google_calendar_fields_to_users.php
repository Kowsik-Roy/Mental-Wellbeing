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
        
        if (!Schema::hasColumn('users', 'google_id')) {
            $table->string('google_id')->nullable();
        }

        if (!Schema::hasColumn('users', 'google_token')) {
            $table->string('google_token')->nullable();
        }

        if (!Schema::hasColumn('users', 'google_refresh_token')) {
            $table->string('google_refresh_token')->nullable();
        }

        if (!Schema::hasColumn('users', 'calendar_sync_enabled')) {
            $table->boolean('calendar_sync_enabled')->default(false);
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
