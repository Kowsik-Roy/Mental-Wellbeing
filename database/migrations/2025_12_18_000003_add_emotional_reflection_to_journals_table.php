<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            if (!Schema::hasColumn('journals', 'emotional_reflection')) {
                $table->text('emotional_reflection')->nullable()->after('mood');
            }
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn('emotional_reflection');
        });
    }
};
