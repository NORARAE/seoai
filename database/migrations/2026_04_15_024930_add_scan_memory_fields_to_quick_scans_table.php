<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->unsignedSmallInteger('last_score')->nullable()->after('score');
            $table->smallInteger('score_change')->nullable()->after('last_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropColumn(['last_score', 'score_change']);
        });
    }
};
