<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitor_gaps', function (Blueprint $table): void {
            $table->foreignId('competitor_domain_id')->nullable()->after('site_id')->constrained('competitor_domains')->nullOnDelete();
            $table->foreignId('site_scan_run_id')->nullable()->after('competitor_domain_id')->constrained('scan_runs')->nullOnDelete();
            $table->foreignId('competitor_scan_run_id')->nullable()->after('site_scan_run_id')->constrained('competitor_scan_runs')->nullOnDelete();
            $table->boolean('is_current')->default(true)->after('status');
            $table->index(['site_id', 'site_scan_run_id', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::table('competitor_gaps', function (Blueprint $table): void {
            $table->dropIndex(['site_id', 'site_scan_run_id', 'is_current']);
            $table->dropConstrainedForeignId('competitor_domain_id');
            $table->dropConstrainedForeignId('site_scan_run_id');
            $table->dropConstrainedForeignId('competitor_scan_run_id');
            $table->dropColumn('is_current');
        });
    }
};