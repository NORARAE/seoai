<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_opportunities', function (Blueprint $table) {
            // Strategic category (broader than opportunity_type)
            $table->enum('opportunity_category', [
                'missing_page',           // No page exists for this service × location
                'optimization_candidate', // Page exists but signals show weakness
                'structural_weakness',    // Orphan, no internal links, zero schema
                'coverage_gap',           // Competitor covers this, we do not
            ])->nullable()->after('opportunity_type')
                ->comment('Strategic grouping used by SearchOpportunityEngine');

            // Link to discovered crawl URL (optional — populated when a crawled URL maps to this opportunity)
            $table->foreignId('url_inventory_id')->nullable()->after('payload_id')
                ->constrained('url_inventory')->nullOnDelete();

            // Explainable score components (all 0–100, nullable so Phase 2 can enrich)
            $table->decimal('demand_score', 5, 2)->nullable()->after('priority_score')
                ->comment('Demand signal: search_volume / impressions / GSC data. Phase 2: keyword API.');
            $table->decimal('readiness_score', 5, 2)->nullable()->after('demand_score')
                ->comment('Content readiness: title, h1, schema, word_count quality signals.');
            $table->decimal('business_value_score', 5, 2)->nullable()->after('readiness_score')
                ->comment('Market size × service value proxy.');
            $table->decimal('risk_score', 5, 2)->nullable()->after('business_value_score')
                ->comment('Risk of acting on this opportunity (low = safe, high = could regress).');
            $table->decimal('total_score', 5, 2)->nullable()->after('risk_score')
                ->comment('Weighted total: demand×0.35 + readiness×0.25 + business×0.25 + (100-risk)×0.15');

            // Explainability payloads
            $table->json('score_components')->nullable()->after('total_score')
                ->comment('Full score breakdown JSON for UI display.');
            $table->json('signals')->nullable()->after('score_components')
                ->comment('Raw signal snapshot collected during analysis.');

            // Human-readable output
            $table->text('reason_summary')->nullable()->after('signals')
                ->comment('1-2 sentence explanation of why this opportunity was detected.');
            $table->text('recommended_action')->nullable()->after('reason_summary')
                ->comment('Concrete next step recommendation.');

            $table->index('opportunity_category');
            $table->index('total_score');
            $table->index('url_inventory_id');
        });
    }

    public function down(): void
    {
        Schema::table('seo_opportunities', function (Blueprint $table) {
            $table->dropForeign(['url_inventory_id']);
            $table->dropIndex(['opportunity_category']);
            $table->dropIndex(['total_score']);
            $table->dropIndex(['url_inventory_id']);
            $table->dropColumn([
                'opportunity_category',
                'url_inventory_id',
                'demand_score',
                'readiness_score',
                'business_value_score',
                'risk_score',
                'total_score',
                'score_components',
                'signals',
                'reason_summary',
                'recommended_action',
            ]);
        });
    }
};
