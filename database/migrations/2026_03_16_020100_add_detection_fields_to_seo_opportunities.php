<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_opportunities', function (Blueprint $table): void {
            $table->string('target_keyword')->nullable()->after('opportunity_type')
                ->comment('Primary keyword this opportunity targets');
            $table->string('suggested_url')->nullable()->after('target_keyword')
                ->comment('Recommended URL slug for the new/optimised page');
            $table->string('detection_source')->nullable()->after('suggested_url')
                ->comment('Origin of detection: crawl_discovery | manual | gsync');
        });
    }

    public function down(): void
    {
        Schema::table('seo_opportunities', function (Blueprint $table): void {
            $table->dropColumn(['target_keyword', 'suggested_url', 'detection_source']);
        });
    }
};
