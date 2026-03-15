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
        Schema::create('seo_opportunities', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('location_id')->comment('References cities.id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            
            // SEO Metrics
            $table->integer('search_volume')->default(0)->comment('Estimated monthly searches');
            $table->decimal('competition_score', 5, 2)->default(0)->comment('Competition level 0-100');
            $table->decimal('rank_potential', 5, 2)->default(0)->comment('Ranking potential 0-100');
            $table->decimal('priority_score', 5, 2)->default(0)->comment('Overall opportunity priority 0-100');
            
            // Revenue Metrics
            $table->decimal('estimated_monthly_revenue', 10, 2)->nullable()->comment('Projected monthly revenue');
            $table->decimal('service_value', 10, 2)->nullable()->comment('Average service value/conversion');
            $table->decimal('conversion_rate', 5, 4)->default(0.02)->comment('Expected conversion rate (default 2%)');
            
            // Current Performance (if page exists)
            $table->foreignId('location_page_id')->nullable()->constrained('location_pages')->onDelete('set null');
            $table->boolean('page_exists')->default(false);
            $table->integer('current_position')->nullable()->comment('Current avg position in GSC');
            $table->integer('current_impressions')->nullable()->comment('Current monthly impressions');
            $table->integer('current_clicks')->nullable()->comment('Current monthly clicks');
            $table->decimal('current_ctr', 5, 4)->nullable()->comment('Current CTR');
            
            // Opportunity Classification
            $table->enum('opportunity_type', [
                'new_page',          // No page exists - pure opportunity
                'underperforming',   // Page exists but ranking poorly
                'high_volume',       // High search volume opportunity
                'quick_win',         // Low competition + high potential
                'content_gap',       // Competitor coverage but we're missing
            ])->default('new_page');
            
            $table->enum('status', [
                'pending',           // Discovered but not reviewed
                'approved',          // Approved for action
                'in_progress',       // Page generation in progress
                'completed',         // Page generated
                'dismissed',         // Not worth pursuing
                'monitoring',        // Completed and tracking performance
            ])->default('pending');
            
            // Analysis Data
            $table->json('competitor_analysis')->nullable()->comment('Competitor ranking data');
            $table->json('keyword_data')->nullable()->comment('Related keywords and volumes');
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamp('identified_at')->useCurrent();
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['site_id', 'priority_score']);
            $table->index(['status', 'priority_score']);
            $table->index(['opportunity_type', 'status']);
            $table->unique(['site_id', 'service_id', 'location_id'], 'unique_opportunity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_opportunities');
    }
};
