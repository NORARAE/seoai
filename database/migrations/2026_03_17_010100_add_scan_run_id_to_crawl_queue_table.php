<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crawl_queue', function (Blueprint $table) {
            $table->foreignId('scan_run_id')
                ->nullable()
                ->after('site_id')
                ->constrained('scan_runs')
                ->nullOnDelete();

            $table->index('scan_run_id');
        });
    }

    public function down(): void
    {
        Schema::table('crawl_queue', function (Blueprint $table) {
            $table->dropForeign(['scan_run_id']);
            $table->dropColumn('scan_run_id');
        });
    }
};
