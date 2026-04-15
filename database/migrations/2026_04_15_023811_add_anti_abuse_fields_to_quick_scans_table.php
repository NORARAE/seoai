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
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->boolean('is_repeat_scan')->default(false)->after('suppress_emails');
            $table->string('domain', 255)->nullable()->after('url');
            $table->unsignedSmallInteger('domain_scan_count')->default(1)->after('is_repeat_scan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quick_scans', function (Blueprint $table) {
            $table->dropColumn(['is_repeat_scan', 'domain', 'domain_scan_count']);
        });
    }
};
