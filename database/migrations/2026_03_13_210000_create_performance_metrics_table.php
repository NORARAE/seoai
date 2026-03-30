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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            
            // Optional foreign keys - not all GSC URLs map to existing pages
            $table->foreignId('page_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('location_page_id')->nullable()->constrained()->onDelete('cascade');
            
            // Raw URL from GSC
            $table->string('url', 500)->index();
            
            // Search query (nullable for page-level aggregates)
            $table->string('query', 500)->nullable();
            
            // Date of the metric
            $table->date('date')->index();
            
            // Core GSC metrics
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->decimal('ctr', 5, 4)->default(0); // e.g., 0.0342 = 3.42%
            $table->decimal('average_position', 5, 2)->default(0); // e.g., 7.35
            
            // Optional dimensions
            $table->string('device', 20)->nullable(); // DESKTOP, MOBILE, TABLET
            $table->char('country', 2)->nullable(); // ISO 3166-1 alpha-2
            
            $table->timestamps();
            
            // Compound indexes for common queries
            $table->index(['site_id', 'date']);
            $table->index(['page_id', 'date'], 'performance_metrics_page_date');
            $table->index(['location_page_id', 'date'], 'performance_metrics_location_page_date');
            $table->index(['url', 'site_id', 'date']);
            $table->index(['query', 'site_id', 'date']);
            
            // For opportunity detection (high impressions, low CTR)
            $table->index(['impressions', 'ctr']);
            
            // Unique constraint to prevent duplicate imports (hash to avoid key length limits)
            $table->string('import_hash', 64)->nullable()->unique('performance_unique')->comment('SHA256 of site_id|url|query|date|device|country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
