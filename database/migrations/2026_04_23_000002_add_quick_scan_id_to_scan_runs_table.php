<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scan_runs', function (Blueprint $table): void {
            // FK back to the QuickScan that triggered this crawl.
            // Null for admin-initiated or scheduled crawls.
            $table->foreignId('quick_scan_id')
                ->nullable()
                ->after('site_id')
                ->constrained('quick_scans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scan_runs', function (Blueprint $table): void {
            $table->dropForeign(['quick_scan_id']);
            $table->dropColumn('quick_scan_id');
        });
    }
};
