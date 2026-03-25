<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->boolean('sitemap_enabled')->default(true)->after('connection_test_error');
            $table->boolean('sitemap_include_payload_pages')->default(true)->after('sitemap_enabled');
            $table->boolean('sitemap_include_discovered_pages')->default(true)->after('sitemap_include_payload_pages');
            $table->text('sitemap_manual_include_urls')->nullable()->after('sitemap_include_discovered_pages');
            $table->text('sitemap_manual_exclude_urls')->nullable()->after('sitemap_manual_include_urls');
            $table->unsignedInteger('sitemap_max_urls_per_file')->default(500)->after('sitemap_manual_exclude_urls');
            $table->timestamp('gsc_last_sitemap_submission_at')->nullable()->after('gsc_sync_error');
            $table->string('gsc_last_sitemap_submission_status')->nullable()->after('gsc_last_sitemap_submission_at');
            $table->text('gsc_last_sitemap_submission_error')->nullable()->after('gsc_last_sitemap_submission_status');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'sitemap_enabled',
                'sitemap_include_payload_pages',
                'sitemap_include_discovered_pages',
                'sitemap_manual_include_urls',
                'sitemap_manual_exclude_urls',
                'sitemap_max_urls_per_file',
                'gsc_last_sitemap_submission_at',
                'gsc_last_sitemap_submission_status',
                'gsc_last_sitemap_submission_error',
            ]);
        });
    }
};