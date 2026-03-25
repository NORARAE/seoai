<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_scan_urls', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('competitor_domain_id')->constrained('competitor_domains')->cascadeOnDelete();
            $table->foreignId('competitor_scan_run_id')->constrained('competitor_scan_runs')->cascadeOnDelete();
            $table->string('url', 2048);
            $table->string('normalized_url', 2048);
            $table->string('path')->nullable();
            $table->string('source')->default('sitemap');
            $table->timestamps();

            $table->index(['competitor_scan_run_id', 'path']);
            $table->unique(['competitor_scan_run_id', 'normalized_url']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_scan_urls');
    }
};