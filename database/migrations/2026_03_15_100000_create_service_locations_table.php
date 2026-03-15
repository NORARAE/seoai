<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tracks service × location coverage matrix with calculated metrics
     */
    public function up(): void
    {
        Schema::create('service_locations', function (Blueprint $table) {
            $table->id();
            
            // Matrix dimensions
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->foreignId('county_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('cascade');
            
            // Coverage tracking
            $table->boolean('page_exists')->default(false);
            $table->foreignId('location_page_id')->nullable()->constrained()->onDelete('set null');
            
            // Intelligence metrics
            $table->unsignedInteger('traffic_potential')->default(0); // 0-100 score
            $table->unsignedInteger('priority_score')->default(0); // 0-100 composite score
            $table->decimal('estimated_monthly_searches', 10, 2)->default(0);
            
            // Performance tracking (if page exists)
            $table->unsignedInteger('avg_impressions_30d')->default(0);
            $table->unsignedInteger('avg_clicks_30d')->default(0);
            $table->decimal('avg_ctr_30d', 5, 4)->default(0); // 0.0000-1.0000
            $table->decimal('avg_position_30d', 5, 2)->default(0);
            
            // Status tracking
            $table->string('status')->default('pending'); // pending, generated, active, low_traffic, no_demand
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamp('page_generated_at')->nullable();
            
            // Metadata
            $table->json('analysis_data')->nullable(); // Store detailed analysis
            
            $table->timestamps();
            
            // Unique constraint: one record per service × city combination
            $table->unique(['service_id', 'city_id'], 'service_city_unique');
            
            // Indexes for queries
            $table->index(['service_id', 'page_exists']);
            $table->index(['priority_score', 'page_exists']);
            $table->index(['status', 'priority_score']);
            $table->index(['service_id', 'state_id', 'county_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_locations');
    }
};
