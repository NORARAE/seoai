<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('url_inventory', function (Blueprint $table) {
            // The scan run during which this URL was first discovered.
            $table->foreignId('first_seen_scan_run_id')
                ->nullable()
                ->after('site_id')
                ->constrained('scan_runs')
                ->nullOnDelete();

            // The most recent scan run that confirmed this URL still exists.
            $table->foreignId('last_seen_scan_run_id')
                ->nullable()
                ->after('first_seen_scan_run_id')
                ->constrained('scan_runs')
                ->nullOnDelete();

            $table->index('first_seen_scan_run_id');
            $table->index('last_seen_scan_run_id');
        });
    }

    public function down(): void
    {
        Schema::table('url_inventory', function (Blueprint $table) {
            $table->dropForeign(['first_seen_scan_run_id']);
            $table->dropForeign(['last_seen_scan_run_id']);
            $table->dropColumn(['first_seen_scan_run_id', 'last_seen_scan_run_id']);
        });
    }
};
