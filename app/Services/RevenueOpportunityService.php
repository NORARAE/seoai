<?php

namespace App\Services;

use App\Models\City;
use App\Models\LocationPage;
use App\Models\PerformanceMetric;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RevenueOpportunityService
 * 
 * Identifies and scores SEO revenue opportunities
 * Analyzes service × location combinations for revenue potential
 */
class RevenueOpportunityService
{
    /**
     * Default service value if not specified
     */
    protected float $defaultServiceValue = 500.00;

    /**
     * Default conversion rate (2%)
     */
    protected float $defaultConversionRate = 0.02;

    /**
     * Generate opportunities for a site
     */
    public function generateOpportunities(Site $site, ?array $options = []): array
    {
        $options = array_merge([
            'min_priority_score' => 50,
            'min_search_volume' => 10,
            'service_value' => $this->defaultServiceValue,
            'conversion_rate' => $this->defaultConversionRate,
            'limit' => null,
        ], $options);

        $services = Service::where('is_active', true)->get();
        $cities = City::where('state_id', $site->state_id)->get();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($services as $service) {
            foreach ($cities as $city) {
                // Check if opportunity already exists
                $opportunity = SeoOpportunity::firstOrNew([
                    'site_id' => $site->id,
                    'service_id' => $service->id,
                    'location_id' => $city->id,
                ]);

                $isNew = !$opportunity->exists;

                // Skip if already dismissed or completed with good performance
                if (!$isNew && in_array($opportunity->status, ['dismissed', 'monitoring'])) {
                    $skipped++;
                    continue;
                }

                // Calculate opportunity metrics
                $metrics = $this->calculateOpportunityMetrics($site, $service, $city, $options);

                // Skip low-value opportunities
                if ($metrics['priority_score'] < $options['min_priority_score']) {
                    $skipped++;
                    continue;
                }

                if ($metrics['search_volume'] < $options['min_search_volume']) {
                    $skipped++;
                    continue;
                }

                // Update/create opportunity
                $opportunity->fill([
                    'client_id' => $site->client_id,
                    'search_volume' => $metrics['search_volume'],
                    'competition_score' => $metrics['competition_score'],
                    'rank_potential' => $metrics['rank_potential'],
                    'priority_score' => $metrics['priority_score'],
                    'service_value' => $options['service_value'],
                    'conversion_rate' => $options['conversion_rate'],
                    'page_exists' => $metrics['page_exists'],
                    'location_page_id' => $metrics['location_page_id'],
                    'current_position' => $metrics['current_position'],
                    'current_impressions' => $metrics['current_impressions'],
                    'current_clicks' => $metrics['current_clicks'],
                    'current_ctr' => $metrics['current_ctr'],
                    'opportunity_type' => $metrics['opportunity_type'],
                    'identified_at' => $opportunity->identified_at ?? now(),
                    'last_analyzed_at' => now(),
                ]);

                // Calculate estimated revenue
                $opportunity->estimated_monthly_revenue = $opportunity->calculateEstimatedRevenue();

                $opportunity->save();

                $isNew ? $created++ : $updated++;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'total_processed' => $created + $updated + $skipped,
        ];
    }

    /**
     * Calculate opportunity metrics for a service × location combination
     */
    protected function calculateOpportunityMetrics(
        Site $site,
        Service $service,
        City $city,
        array $options
    ): array {
        // Check if page exists
        $existingPage = LocationPage::where('site_id', $site->id)
            ->where('service_id', $service->id)
            ->where('city_id', $city->id)
            ->first();

        $pageExists = $existingPage !== null;

        // Get current performance if page exists
        $currentMetrics = $this->getCurrentPerformance($existingPage);

        // Calculate search volume (estimated)
        $searchVolume = $this->estimateSearchVolume($city, $service);

        // Calculate competition score
        $competitionScore = $this->calculateCompetitionScore($city, $service, $site);

        // Calculate rank potential
        $rankPotential = $this->calculateRankPotential($city, $service, $existingPage, $competitionScore);

        // Determine opportunity type
        $opportunityType = $this->determineOpportunityType($pageExists, $currentMetrics, $searchVolume, $competitionScore, $rankPotential);

        // Calculate priority score
        $priorityScore = $this->calculatePriorityScore(
            $searchVolume,
            $competitionScore,
            $rankPotential,
            $currentMetrics,
            $opportunityType
        );

        return [
            'page_exists' => $pageExists,
            'location_page_id' => $existingPage?->id,
            'search_volume' => $searchVolume,
            'competition_score' => $competitionScore,
            'rank_potential' => $rankPotential,
            'priority_score' => $priorityScore,
            'opportunity_type' => $opportunityType,
            'current_position' => $currentMetrics['position'],
            'current_impressions' => $currentMetrics['impressions'],
            'current_clicks' => $currentMetrics['clicks'],
            'current_ctr' => $currentMetrics['ctr'],
        ];
    }

    /**
     * Get current performance metrics for a page
     */
    protected function getCurrentPerformance(?LocationPage $page): array
    {
        if (!$page) {
            return [
                'position' => null,
                'impressions' => null,
                'clicks' => null,
                'ctr' => null,
            ];
        }

        // Get 30-day average performance
        $performance = PerformanceMetric::where('page_type', LocationPage::class)
            ->where('page_id', $page->id)
            ->where('date', '>=', now()->subDays(30))
            ->selectRaw('
                AVG(position) as avg_position,
                AVG(impressions) as avg_impressions,
                AVG(clicks) as avg_clicks,
                AVG(ctr) as avg_ctr
            ')
            ->first();

        return [
            'position' => $performance?->avg_position ? round($performance->avg_position, 1) : null,
            'impressions' => $performance?->avg_impressions ? (int) $performance->avg_impressions : null,
            'clicks' => $performance?->avg_clicks ? (int) $performance->avg_clicks : null,
            'ctr' => $performance?->avg_ctr ? round($performance->avg_ctr, 4) : null,
        ];
    }

    /**
     * Estimate search volume for service × location
     */
    protected function estimateSearchVolume(City $city, Service $service): int
    {
        // Base volume on population
        if (!$city->population) {
            return 50; // Default minimum
        }

        // Rough heuristic: 0.1% - 0.5% of population searches monthly
        // Varies by service type
        $searchRate = match(true) {
            $city->population >= 100000 => 0.005, // 0.5%
            $city->population >= 50000 => 0.003,  // 0.3%
            $city->population >= 25000 => 0.002,  // 0.2%
            default => 0.001,                      // 0.1%
        };

        $estimatedVolume = (int) ($city->population * $searchRate);

        // Apply service multiplier (some services searched more than others)
        // This could be enhanced with actual keyword data
        return max(10, $estimatedVolume);
    }

    /**
     * Calculate competition score (0-100)
     */
    protected function calculateCompetitionScore(City $city, Service $service, Site $site): float
    {
        $score = 50; // Base competition level

        // Check existing pages in same county
        $countyCompetition = ServiceLocation::where('county_id', $city->county_id)
            ->where('service_id', $service->id)
            ->where('page_exists', true)
            ->count();

        // More existing pages = higher competition
        if ($countyCompetition >= 5) {
            $score += 20;
        } elseif ($countyCompetition >= 3) {
            $score += 10;
        }

        // Check if similar pages are performing well (high competition)
        $nearbyPerformance = ServiceLocation::where('county_id', $city->county_id)
            ->where('service_id', $service->id)
            ->where('page_exists', true)
            ->where('avg_impressions_30d', '>', 500)
            ->count();

        if ($nearbyPerformance >= 2) {
            $score += 15; // High competition but proven demand
        }

        // Population factor (larger cities = more competition)
        if ($city->population >= 100000) {
            $score += 15;
        } elseif ($city->population >= 50000) {
            $score += 10;
        }

        return min(100, round($score, 2));
    }

    /**
     * Calculate rank potential (0-100)
     */
    protected function calculateRankPotential(
        City $city,
        Service $service,
        ?LocationPage $existingPage,
        float $competitionScore
    ): float {
        $potential = 70; // Base potential

        // If page exists with good position, higher potential
        if ($existingPage) {
            $currentMetrics = $this->getCurrentPerformance($existingPage);
            
            if ($currentMetrics['position']) {
                if ($currentMetrics['position'] <= 3) {
                    $potential = 95;
                } elseif ($currentMetrics['position'] <= 5) {
                    $potential = 85;
                } elseif ($currentMetrics['position'] <= 10) {
                    $potential = 75;
                } else {
                    $potential = 60;
                }
            }
        } else {
            // New page potential based on competition
            if ($competitionScore < 40) {
                $potential = 85; // Low competition = high potential
            } elseif ($competitionScore < 60) {
                $potential = 70;
            } else {
                $potential = 55; // High competition = lower potential
            }
        }

        // Geographic diversity bonus
        $existingInCounty = ServiceLocation::where('county_id', $city->county_id)
            ->where('service_id', $service->id)
            ->where('page_exists', true)
            ->count();

        if ($existingInCounty == 0) {
            $potential += 10; // First in county
        }

        return min(100, round($potential, 2));
    }

    /**
     * Determine opportunity type
     */
    protected function determineOpportunityType(
        bool $pageExists,
        array $currentMetrics,
        int $searchVolume,
        float $competitionScore,
        float $rankPotential
    ): string {
        if (!$pageExists) {
            // Quick win: High potential + low competition
            if ($rankPotential >= 75 && $competitionScore < 50) {
                return 'quick_win';
            }
            
            // High volume opportunity
            if ($searchVolume >= 200) {
                return 'high_volume';
            }
            
            return 'new_page';
        }

        // Page exists - check performance
        if ($currentMetrics['position'] && $currentMetrics['position'] > 10) {
            return 'underperforming';
        }

        if ($searchVolume >= 200 && $currentMetrics['impressions'] < $searchVolume * 0.5) {
            return 'content_gap';
        }

        return 'new_page';
    }

    /**
     * Calculate priority score (0-100)
     */
    protected function calculatePriorityScore(
        int $searchVolume,
        float $competitionScore,
        float $rankPotential,
        array $currentMetrics,
        string $opportunityType
    ): float {
        $score = 0;

        // Search volume impact (30%)
        $volumeScore = min(30, ($searchVolume / 500) * 30);
        $score += $volumeScore;

        // Rank potential impact (40%)
        $score += ($rankPotential / 100) * 40;

        // Competition impact (20%) - inverse relationship
        $competitionImpact = (100 - $competitionScore) / 100;
        $score += $competitionImpact * 20;

        // Opportunity type bonus (10%)
        $typeBonus = match($opportunityType) {
            'quick_win' => 10,
            'high_volume' => 8,
            'new_page' => 5,
            'underperforming' => 7,
            'content_gap' => 6,
            default => 0,
        };
        $score += $typeBonus;

        // Existing page with poor performance gets priority boost
        if ($currentMetrics['position'] && $currentMetrics['position'] > 20) {
            $score += 5;
        }

        return min(100, round($score, 2));
    }

    /**
     * Get top revenue opportunities for a site
     */
    public function getTopOpportunities(Site $site, int $limit = 20): Collection
    {
        return SeoOpportunity::where('site_id', $site->id)
            ->topOpportunities($limit)
            ->with(['service', 'location', 'locationPage'])
            ->get();
    }

    /**
     * Get opportunities by type
     */
    public function getOpportunitiesByType(Site $site, string $type): Collection
    {
        return SeoOpportunity::where('site_id', $site->id)
            ->where('opportunity_type', $type)
            ->where('status', '!=', 'dismissed')
            ->orderBy('priority_score', 'desc')
            ->with(['service', 'location'])
            ->get();
    }

    /**
     * Get quick wins (low competition + high potential)
     */
    public function getQuickWins(Site $site, int $limit = 10): Collection
    {
        return SeoOpportunity::where('site_id', $site->id)
            ->quickWins()
            ->limit($limit)
            ->with(['service', 'location'])
            ->get();
    }

    /**
     * Get high revenue opportunities
     */
    public function getHighRevenueOpportunities(Site $site, float $minRevenue = 100, int $limit = 20): Collection
    {
        return SeoOpportunity::where('site_id', $site->id)
            ->highRevenue($minRevenue)
            ->where('status', '!=', 'dismissed')
            ->orderBy('estimated_monthly_revenue', 'desc')
            ->limit($limit)
            ->with(['service', 'location'])
            ->get();
    }

    /**
     * Update opportunity with actual page performance
     */
    public function updateOpportunityPerformance(SeoOpportunity $opportunity): void
    {
        if (!$opportunity->location_page_id) {
            return;
        }

        $page = LocationPage::find($opportunity->location_page_id);
        if (!$page) {
            return;
        }

        $currentMetrics = $this->getCurrentPerformance($page);
        $opportunity->updatePerformanceMetrics($currentMetrics);

        // If performing well, move to monitoring
        if ($currentMetrics['impressions'] >= 100 && $currentMetrics['position'] <= 10) {
            $opportunity->update(['status' => 'monitoring']);
        }
    }

    /**
     * Refresh all opportunities for a site
     */
    public function refreshOpportunities(Site $site, array $options = []): array
    {
        // Re-analyze existing opportunities
        $existing = SeoOpportunity::where('site_id', $site->id)
            ->whereIn('status', ['pending', 'approved', 'monitoring'])
            ->get();

        $refreshed = 0;
        foreach ($existing as $opportunity) {
            $this->updateOpportunityPerformance($opportunity);
            $refreshed++;
        }

        // Generate new opportunities
        $generated = $this->generateOpportunities($site, $options);

        return array_merge($generated, ['refreshed' => $refreshed]);
    }
}
