<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitor_domains', function (Blueprint $table): void {
            $table->unsignedInteger('scan_count')->default(0)->after('domain');
            $table->unsignedInteger('paid_scan_credits')->default(0)->after('scan_count');
            $table->timestamp('last_scanned_at')->nullable()->after('paid_scan_credits');
        });
    }

    public function down(): void
    {
        Schema::table('competitor_domains', function (Blueprint $table): void {
            $table->dropColumn(['scan_count', 'paid_scan_credits', 'last_scanned_at']);
        });
    }
};