<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quick_scans', function (Blueprint $table): void {
            // FK to sites — nullable because many quick_scans predate the crawler
            // and because a scan might fail before a Site is created.
            $table->foreignId('site_id')
                ->nullable()
                ->after('user_id')
                ->constrained('sites')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table): void {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
        });
    }
};
