<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\Site;
use App\Models\Page;
use App\Models\LocationPage;
use App\Models\PerformanceMetric;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * OpportunityDetectionService
 * 
 * Analyzes performance data to detect SEO opportunities
 * including low CTR pages, high impressions, missing pages, etc.
 */
class OpportunityDetectionService
{
    /**
     * Scan all sites for opportunities (or specific site)
     */
    public function scanSites(?Site $site = null): array
    {
        $sites = $site ? collect([$site]) : Site::where('is_active', true)->get();
        
        $results = [
            'sites_scanned' => 0,
            'opportunities_detected' => 0,
            'opportunities_by_type' => [],
        ];

        foreach ($sites as $scanSite) {
            $siteResults = $this->scanSite($scanSite);
            
            $results['sites_scanned']++;
            $results['opportunities_detected'] += $siteResults['total'];
            
            foreach ($siteResults['by_type'] as $type => $count) {
                $results['opportunities_by_type'][$type] = 
                    ($results['opportunities_by_type'][$type] ?? 0) + $count;
            }
        }

        return $results;
    }

    /**
     * Scan a single site for opportunities
     */
    public function scanSite(Site $site): array
    {
        $results = [
            'total' => 0,
            'by_type' => [],
        ];

        // Detect low CTR opportunities
        $lowCtrOpps = $this->detectLowCtrOpportunities($site);
        $results['by_type']['low_ctr'] = $lowCtrOpps->count();
        $results['total'] += $lowCtrOpps->count();

        // Detect high impressions with low clicks
        $highImpOpps = $this->detectHighImpressionOpportunities($site);
        $results['by_type']['high_impressions'] = $highImpOpps->count();
        $results['total'] += $highImpOpps->count();

        // Detect thin content opportunities
        $thinContentOpps = $this->detectThinContentOpportunities($site);
        $results['by_type']['thin_content'] = $thinContentOpps->count();
        $results['total'] += $thinContentOpps->count();

        // Detect missing page opportunities (queries without pages)
        $missingPageOpps = $this->detectMissingPageOpportunities($site);
        $results['by_type']['missing_page'] = $missingPageOpps->count();
        $results['total'] += $missingPageOpps->count();

        return $results;
    }

    /**
     * Detect pages with low CTR but decent visibility
     * 
     * Criteria:
     * - Impressions > 1000
     * - CTR < 2%
     * - Position < 10 (first page)
     */
    public function detectLowCtrOpportunities(Site $site): Collection
    {
        $minImpressions = 1000;
        $maxCtr = 0.02; // 2%
        $maxPosition = 10;

        // Get recent performance aggregates (last 30 days)
        $lowCtrPages = PerformanceMetric::select([
                'page_type',
                'page_id',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('AVG(position) as avg_position'),
                DB::raw('SUM(clicks) / SUM(impressions) as ctr'),
            ])
            ->where('site_id', $site->id)
            ->where('date', '>=', now()->subDays(30))
            ->whereNotNull('page_type')
            ->whereNotNull('page_id')
            ->groupBy('page_type', 'page_id')
            ->having('total_impressions', '>', $minImpressions)
            ->having('ctr', '<', $maxCtr)
            ->having('avg_position', '<', $maxPosition)
            ->get();

        $opportunities = [];

        foreach ($lowCtrPages as $pageData) {
            // Check if opportunity already exists and is open
            $existing = Opportunity::where('site_id', $site->id)
                ->where('opportunifiable_type', $pageData->page_type)
                ->where('opportunifiable_id', $pageData->page_id)
                ->where('type', 'low_ctr')
                ->where('status', 'open')
                ->first();

            if ($existing) {
                // Update metrics
                $existing->update([
                    'metrics' => [
                        'impressions' => $pageData->total_impressions,
                        'clicks' => $pageData->total_clicks,
                        'ctr' => round($pageData->ctr * 100, 2),
                        'avg_position' => round($pageData->avg_position, 1),
                    ],
                    'score' => $this->calculateLowCtrScore($pageData),
                ]);
                continue;
            }

            // Create new opportunity
            $opportunity = Opportunity::create([
                'site_id' => $site->id,
                'opportunifiable_type' => $pageData->page_type,
                'opportunifiable_id' => $pageData->page_id,
                'type' => 'low_ctr',
                'priority_score' => (int) $pageData->total_impressions, // Higher impressions = higher priority
                'score' => $this->calculateLowCtrScore($pageData),
                'status' => 'open',
                'recommendation' => $this->generateLowCtrRecommendation($pageData),
                'description' => sprintf(
                    'Page has high visibility (%d impressions) but low CTR (%.2f%%). Optimizing title and meta description could significantly increase traffic.',
                    $pageData->total_impressions,
                    $pageData->ctr * 100
                ),
                'metrics' => [
                    'impressions' => $pageData->total_impressions,
                    'clicks' => $pageData->total_clicks,
                    'ctr' => round($pageData->ctr * 100, 2),
                    'avg_position' => round($pageData->avg_position, 1),
                ],
            ]);

            $opportunities[] = $opportunity;
        }

        return collect($opportunities);
    }

    /**
     * Detect pages with very high impressions regardless of CTR
     */
    public function detectHighImpressionOpportunities(Site $site): Collection
    {
        $minImpressions = 5000; // Threshold for "high impressions"

        $highImpPages = PerformanceMetric::select([
                'page_type',
                'page_id',
                DB::raw('SUM(impressions) as total_impressions'),
                DB::raw('SUM(clicks) as total_clicks'),
                DB::raw('AVG(position) as avg_position'),
            ])
            ->where('site_id', $site->id)
            ->where('date', '>=', now()->subDays(30))
            ->whereNotNull('page_type')
            ->whereNotNull('page_id')
            ->groupBy('page_type', 'page_id')
            ->having('total_impressions', '>', $minImpressions)
            ->get();

        $opportunities = [];

        foreach ($highImpPages as $pageData) {
            // Don't duplicate if already flagged as low_ctr
            $existing = Opportunity::where('site_id', $site->id)
                ->where('opportunifiable_type', $pageData->page_type)
                ->where('opportunifiable_id', $pageData->page_id)
                ->whereIn('type', ['low_ctr', 'high_impressions'])
                ->where('status', 'open')
                ->first();

            if ($existing) {
                continue;
            }

            $opportunity = Opportunity::create([
                'site_id' => $site->id,
                'opportunifiable_type' => $pageData->page_type,
                'opportunifiable_id' => $pageData->page_id,
                'type' => 'high_impressions',
                'priority_score' => (int) $pageData->total_impressions,
                'score' => min(100, $pageData->total_impressions / 100), // Normalize to 100
                'status' => 'open',
                'recommendation' => sprintf(
                    'This page receives substantial impressions (%d). Even small CTR improvements could yield significant traffic gains. Consider title optimization and featured snippet targeting.',
                    $pageData->total_impressions
                ),
                'description' => sprintf(
                    'High-visibility page with %d impressions in the last 30 days.',
                    $pageData->total_impressions
                ),
                'metrics' => [
                    'impressions' => $pageData->total_impressions,
                    'clicks' => $pageData->total_clicks,
                    'avg_position' => round($pageData->avg_position, 1),
                ],
            ]);

            $opportunities[] = $opportunity;
        }

        return collect($opportunities);
    }

    /**
     * Detect pages with thin content (LocationPages only)
     */
    public function detectThinContentOpportunities(Site $site): Collection
    {
        $opportunities = [];

        // Get LocationPages with low word count
        $thinPages = LocationPage::where('site_id', $site->id)
            ->where('status', 'published')
            ->get()
            ->filter(function ($page) {
                // Check if content is thin (< 300 words)
                $bodyData = is_string($page->body_json) ? json_decode($page->body_json, true) : $page->body_json;
                $wordCount = 0;

                if (is_array($bodyData)) {
                    foreach ($bodyData as $section) {
                        if (isset($section['content'])) {
                            $wordCount += str_word_count(strip_tags($section['content']));
                        }
                    }
                }

                return $wordCount < 300;
            });

        foreach ($thinPages as $page) {
            // Check if already flagged
            $existing = Opportunity::where('site_id', $site->id)
                ->where('opportunifiable_type', LocationPage::class)
                ->where('opportunifiable_id', $page->id)
                ->where('type', 'thin_content')
                ->where('status', 'open')
                ->first();

            if ($existing) {
                continue;
            }

            $opportunity = Opportunity::create([
                'site_id' => $site->id,
                'opportunifiable_type' => LocationPage::class,
                'opportunifiable_id' => $page->id,
                'type' => 'thin_content',
                'priority_score' => 50, // Medium priority
                'score' => 60,
                'status' => 'open',
                'recommendation' => 'Expand content to at least 500 words. Add local context, service details, and customer testimonials.',
                'description' => 'This page has thin content which may impact rankings and user experience.',
                'metrics' => [
                    'estimated_word_count' => $this->estimateWordCount($page),
                ],
            ]);

            $opportunities[] = $opportunity;
        }

        return collect($opportunities);
    }

    /**
     * Detect search queries that don't have corresponding pages
     */
    public function detectMissingPageOpportunities(Site $site): Collection
    {
        // This would require query-level GSC data
        // Placeholder for now - would need to enhance GscSyncService to track queries
        
        return collect([]);
    }

    /**
     * Calculate score for low CTR opportunity (0-100)
     */
    protected function calculateLowCtrScore($pageData): float
    {
        // Higher impressions = higher opportunity value
        $impressionScore = min(50, $pageData->total_impressions / 100);
        
        // Better position = higher potential impact
        $positionScore = max(0, 50 - ($pageData->avg_position * 5));
        
        return min(100, $impressionScore + $positionScore);
    }

    /**
     * Generate recommendation text for low CTR pages
     */
    protected function generateLowCtrRecommendation($pageData): string
    {
        $ctrPct = round($pageData->ctr * 100, 2);
        $avgPos = round($pageData->avg_position, 1);

        return sprintf(
            "Optimize title and meta description to improve %s%% CTR. Current average position is %s - page is visible but not compelling. Estimated potential: +%d monthly clicks with 4%% CTR.",
            $ctrPct,
            $avgPos,
            (int) ($pageData->total_impressions * 0.02) // Estimated gain with 2% increase
        );
    }

    /**
     * Estimate word count for a page
     */
    protected function estimateWordCount($page): int
    {
        $bodyData = is_string($page->body_json) ? json_decode($page->body_json, true) : $page->body_json;
        $wordCount = 0;

        if (is_array($bodyData)) {
            foreach ($bodyData as $section) {
                if (isset($section['content'])) {
                    $wordCount += str_word_count(strip_tags($section['content']));
                }
            }
        }

        return $wordCount;
    }
}
