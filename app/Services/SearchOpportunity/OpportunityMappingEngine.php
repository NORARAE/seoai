<?php

namespace App\Services\SearchOpportunity;

use App\Models\SeoOpportunity;
use App\Models\Site;
use Illuminate\Support\Collection;

class OpportunityMappingEngine
{
    public function __construct(
        protected CoverageAnalyzer $coverageAnalyzer,
        protected OpportunityScorer $opportunityScorer,
        protected RecommendationBuilder $recommendationBuilder,
    ) {
    }

    public function mapSite(Site $site): array
    {
        $facts = $this->coverageAnalyzer->analyze($site);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $byCategory = [];

        foreach ($facts as $fact) {
            $category = $this->detectCategory($fact);

            if (! $category) {
                $skipped++;
                continue;
            }

            $score = $this->opportunityScorer->score($fact, $category);

            if ($score['total_score'] < 25) {
                $skipped++;
                continue;
            }

            $recommendation = $this->recommendationBuilder->build($fact, $category);
            $saved = $this->upsertOpportunity($site, $fact, $category, $score, $recommendation);

            if ($saved->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }

            $byCategory[$category] = ($byCategory[$category] ?? 0) + 1;
        }

        return [
            'site_id' => $site->id,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'by_category' => $byCategory,
        ];
    }

    protected function detectCategory(array $fact): ?string
    {
        $signals = $fact['signals'] ?? [];

        if (! ($fact['page_exists'] ?? false)) {
            return 'missing_page';
        }

        $metadataWeak = ! ($signals['title_present'] ?? false)
            || ! ($signals['meta_description_present'] ?? false)
            || ! ($signals['h1_present'] ?? false)
            || ! ($signals['schema_present'] ?? false)
            || (int) ($signals['word_count'] ?? 0) < 300;

        $lowCtrWithDemand = (int) ($signals['impressions_30d'] ?? 0) >= 300
            && (float) ($signals['ctr_30d'] ?? 0) < 2.0;

        if ($metadataWeak || $lowCtrWithDemand) {
            return 'optimization_candidate';
        }

        $orphanLike = (bool) ($signals['is_orphan_page'] ?? false)
            || (int) ($signals['incoming_link_count'] ?? 0) === 0
            || ((int) ($signals['url_depth'] ?? 0) >= 4 && (int) ($signals['internal_link_count'] ?? 0) <= 1);

        if ($orphanLike) {
            return 'structural_weakness';
        }

        return null;
    }

    protected function upsertOpportunity(
        Site $site,
        array $fact,
        string $category,
        array $score,
        array $recommendation,
    ): SeoOpportunity {
        $payload = $fact['payload'];
        $locationPage = $fact['location_page'];
        $urlInventory = $fact['url_inventory'];
        $performance = $fact['performance'];
        $service = $fact['service'];
        $city = $fact['city'];

        $opportunityType = match ($category) {
            'missing_page' => 'new_page',
            'optimization_candidate' => ((int) (($fact['signals']['impressions_30d'] ?? 0)) >= 300) ? 'quick_win' : 'underperforming',
            'structural_weakness' => 'content_gap',
            default => 'content_gap',
        };

        $opportunity = SeoOpportunity::firstOrNew([
            'site_id' => $site->id,
            'service_id' => $service->id,
            'location_id' => $city->id,
        ]);

        $opportunity->fill([
            'client_id' => $site->client_id,
            'opportunity_category' => $category,
            'url_inventory_id' => $urlInventory?->id,
            'location_page_id' => $locationPage?->id,
            'payload_id' => $payload?->id,
            'page_exists' => (bool) ($fact['page_exists'] ?? false),
            'search_volume' => (int) round(($fact['signals']['impressions_30d'] ?? 0) * 1.2),
            'competition_score' => 0,
            'rank_potential' => max(0, 100 - (float) (($fact['signals']['average_position_30d'] ?? 50) * 5)),
            'priority_score' => $score['priority_score'],
            'demand_score' => $score['demand_score'],
            'readiness_score' => $score['readiness_score'],
            'business_value_score' => $score['business_value_score'],
            'risk_score' => $score['risk_score'],
            'total_score' => $score['total_score'],
            'score_components' => $score['score_components'],
            'signals' => array_merge($fact['signals'], [
                'coverage_confidence' => $fact['coverage_confidence'] ?? null,
            ]),
            'reason_summary' => $recommendation['reason_summary'],
            'recommended_action' => $recommendation['recommended_action'],
            'estimated_monthly_revenue' => null,
            'current_position' => $performance && $performance->average_position !== null ? (int) round($performance->average_position) : null,
            'current_impressions' => $performance ? (int) $performance->impressions : null,
            'current_clicks' => $performance ? (int) $performance->clicks : null,
            'current_ctr' => ($fact['signals']['ctr_30d'] ?? 0) / 100,
            'opportunity_type' => $opportunityType,
            'status' => in_array($opportunity->status, ['completed', 'dismissed', 'monitoring'], true)
                ? $opportunity->status
                : 'pending',
            'identified_at' => $opportunity->identified_at ?? now(),
            'last_analyzed_at' => now(),
            'target_keyword' => sprintf('%s %s', $service->name, $city->name),
            'suggested_url' => $fact['expected_url'],
            'detection_source' => 'search_opportunity_mapping',
        ]);

        $opportunity->save();

        return $opportunity;
    }
}
