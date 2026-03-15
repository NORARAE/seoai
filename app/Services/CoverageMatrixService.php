<?php

namespace App\Services;

use App\Models\City;
use App\Models\County;
use App\Models\LocationPage;
use App\Models\PerformanceMetric;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\State;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CoverageMatrixService
 * 
 * Builds and analyzes service × location coverage matrix
 * Identifies gaps, calculates priority scores, and provides expansion recommendations
 */
class CoverageMatrixService
{
    /**
     * Build or refresh the complete coverage matrix for a state
     */
    public function buildMatrix(State $state, ?Service $service = null): array
    {
        $services = $service ? collect([$service]) : Service::where('is_active', true)->get();
        $cities = City::where('state_id', $state->id)->get();

        $created = 0;
        $updated = 0;

        foreach ($services as $svc) {
            foreach ($cities as $city) {
                $serviceLocation = ServiceLocation::firstOrNew([
                    'service_id' => $svc->id,
                    'city_id' => $city->id,
                ]);

                // Check if page exists
                $existingPage = LocationPage::where('service_id', $svc->id)
                    ->where('city_id', $city->id)
                    ->first();

                $isNew = !$serviceLocation->exists;

                $serviceLocation->fill([
                    'state_id' => $city->state_id,
                    'county_id' => $city->county_id,
                    'page_exists' => $existingPage !== null,
                    'location_page_id' => $existingPage?->id,
                ]);

                // Calculate metrics
                if ($existingPage) {
                    $this->updatePerformanceMetrics($serviceLocation, $existingPage);
                } else {
                    $this->calculatePotentialMetrics($serviceLocation);
                }

                $serviceLocation->last_analyzed_at = now();
                $serviceLocation->save();

                $isNew ? $created++ : $updated++;
            }
        }

        return [
            'state' => $state->name,
            'services' => $services->count(),
            'cities' => $cities->count(),
            'combinations' => $services->count() * $cities->count(),
            'created' => $created,
            'updated' => $updated,
        ];
    }

    /**
     * Update performance metrics for existing pages
     */
    protected function updatePerformanceMetrics(ServiceLocation $serviceLocation, LocationPage $page): void
    {
        // Get 30-day performance summary
        $performance = PerformanceMetric::where('page_type', LocationPage::class)
            ->where('page_id', $page->id)
            ->where('date', '>=', now()->subDays(30))
            ->selectRaw('
                AVG(impressions) as avg_impressions,
                AVG(clicks) as avg_clicks,
                AVG(ctr) as avg_ctr,
                AVG(position) as avg_position
            ')
            ->first();

        if ($performance) {
            $avgImpressions = (int) ($performance->avg_impressions ?? 0);
            $avgClicks = (int) ($performance->avg_clicks ?? 0);
            $avgCtr = (float) ($performance->avg_ctr ?? 0);
            $avgPosition = (float) ($performance->avg_position ?? 0);

            $serviceLocation->avg_impressions_30d = $avgImpressions;
            $serviceLocation->avg_clicks_30d = $avgClicks;
            $serviceLocation->avg_ctr_30d = $avgCtr;
            $serviceLocation->avg_position_30d = $avgPosition;

            // Calculate priority score for existing pages (based on performance)
            $serviceLocation->priority_score = $this->calculateExistingPageScore($avgImpressions, $avgCtr);

            // Determine status
            if ($avgImpressions < 50) {
                $serviceLocation->status = 'no_demand';
            } elseif ($avgImpressions < 100) {
                $serviceLocation->status = 'low_traffic';
            } else {
                $serviceLocation->status = 'active';
            }
        } else {
            // Page exists but no performance data yet
            $serviceLocation->status = 'generated';
            $serviceLocation->priority_score = 50; // Medium priority
        }
    }

    /**
     * Calculate potential metrics for missing pages
     */
    protected function calculatePotentialMetrics(ServiceLocation $serviceLocation): void
    {
        $city = $serviceLocation->city;
        
        if (!$city) {
            $serviceLocation->traffic_potential = 0;
            $serviceLocation->priority_score = 0;
            $serviceLocation->status = 'pending';
            return;
        }

        // Calculate traffic potential based on city characteristics
        $trafficPotential = $this->calculateTrafficPotential($city);
        $serviceLocation->traffic_potential = $trafficPotential;

        // Calculate priority score for missing pages
        $serviceLocation->priority_score = $this->calculateMissingPageScore($city, $trafficPotential);

        // Estimate monthly searches (rough heuristic)
        $serviceLocation->estimated_monthly_searches = $this->estimateMonthlySearches($city);

        $serviceLocation->status = 'pending';
    }

    /**
     * Calculate traffic potential score (0-100)
     */
    protected function calculateTrafficPotential(City $city): int
    {
        $score = 0;

        // Population-based scoring (max 50 points)
        if ($city->population) {
            if ($city->population >= 100000) {
                $score += 50;
            } elseif ($city->population >= 50000) {
                $score += 40;
            } elseif ($city->population >= 25000) {
                $score += 30;
            } elseif ($city->population >= 10000) {
                $score += 20;
            } else {
                $score += 10;
            }
        } else {
            $score += 15; // Default if no population data
        }

        // County seat or major city bonus (max 20 points)
        // This would require additional data, placeholder for now
        $score += 10;

        // Geographic diversity bonus (max 30 points)
        // Check existing coverage in county
        $existingInCounty = ServiceLocation::where('county_id', $city->county_id)
            ->where('page_exists', true)
            ->count();

        if ($existingInCounty == 0) {
            $score += 30; // First page in county
        } elseif ($existingInCounty < 3) {
            $score += 15; // Low coverage
        } else {
            $score += 5; // Good coverage
        }

        return min(100, $score);
    }

    /**
     * Calculate priority score for missing pages (0-100)
     */
    protected function calculateMissingPageScore(City $city, int $trafficPotential): int
    {
        $score = 0;

        // Traffic potential weighs heavily (60%)
        $score += ($trafficPotential * 0.6);

        // Competition analysis (20%)
        // Check if similar pages exist nearby
        $nearbySimilarPages = ServiceLocation::where('county_id', $city->county_id)
            ->where('page_exists', true)
            ->where('avg_impressions_30d', '>', 100)
            ->count();

        if ($nearbySimilarPages > 0) {
            $score += 20; // Evidence of demand
        } else {
            $score += 5; // Uncertain demand
        }

        // Strategic value (20%)
        // Favor cities that fill gaps
        $score += 15; // Base strategic value

        return min(100, (int) $score);
    }

    /**
     * Calculate priority score for existing pages
     */
    protected function calculateExistingPageScore(int $impressions, float $ctr): int
    {
        // Existing pages scored on actual performance
        $score = 0;

        // Impression volume (50 points max)
        if ($impressions >= 1000) {
            $score += 50;
        } elseif ($impressions >= 500) {
            $score += 40;
        } elseif ($impressions >= 100) {
            $score += 30;
        } else {
            $score += 10;
        }

        // CTR performance (50 points max)
        if ($ctr >= 0.05) { // 5%+
            $score += 50;
        } elseif ($ctr >= 0.03) { // 3%+
            $score += 35;
        } elseif ($ctr >= 0.02) { // 2%+
            $score += 20;
        } else {
            $score += 10;
        }

        return min(100, $score);
    }

    /**
     * Estimate monthly searches for a service in a city
     */
    protected function estimateMonthlySearches(City $city): float
    {
        if (!$city->population) {
            return 50; // Default estimate
        }

        // Very rough heuristic: searches per capita
        // Assumes ~0.1% of population searches for service monthly
        $searchRate = 0.001;
        
        return round($city->population * $searchRate, 2);
    }

    /**
     * Get coverage matrix for visualization
     */
    public function getMatrix(State $state, ?array $serviceIds = null): array
    {
        $query = ServiceLocation::where('state_id', $state->id)
            ->with(['service', 'city', 'county']);

        if ($serviceIds) {
            $query->whereIn('service_id', $serviceIds);
        }

        $data = $query->get();

        // Group by service and city
        $matrix = [];
        foreach ($data as $item) {
            $serviceId = $item->service_id;
            $cityId = $item->city_id;

            if (!isset($matrix[$serviceId])) {
                $matrix[$serviceId] = [
                    'service' => $item->service,
                    'cities' => [],
                ];
            }

            $matrix[$serviceId]['cities'][$cityId] = [
                'city' => $item->city,
                'county' => $item->county,
                'page_exists' => $item->page_exists,
                'status' => $item->status,
                'status_color' => $item->status_color,
                'status_text' => $item->status_text,
                'priority_score' => $item->priority_score,
                'traffic_potential' => $item->traffic_potential,
                'avg_impressions' => $item->avg_impressions_30d,
                'avg_clicks' => $item->avg_clicks_30d,
                'location_page_id' => $item->location_page_id,
                'service_location_id' => $item->id,
            ];
        }

        return $matrix;
    }

    /**
     * Get top expansion opportunities
     */
    public function getTopOpportunities(State $state, int $limit = 20): Collection
    {
        return ServiceLocation::where('state_id', $state->id)
            ->topOpportunities($limit)
            ->with(['service', 'city', 'county'])
            ->get();
    }

    /**
     * Get coverage statistics
     */
    public function getCoverageStats(State $state): array
    {
        $total = ServiceLocation::where('state_id', $state->id)->count();
        $existing = ServiceLocation::where('state_id', $state->id)->existingPages()->count();
        $missing = $total - $existing;
        $lowTraffic = ServiceLocation::where('state_id', $state->id)->lowTraffic()->count();
        $highPriority = ServiceLocation::where('state_id', $state->id)
            ->missingPages()
            ->highPriority()
            ->count();

        return [
            'total_combinations' => $total,
            'pages_exist' => $existing,
            'pages_missing' => $missing,
            'coverage_percentage' => $total > 0 ? round(($existing / $total) * 100, 1) : 0,
            'low_traffic_pages' => $lowTraffic,
            'high_priority_gaps' => $highPriority,
        ];
    }

    /**
     * Refresh single service location
     */
    public function refreshServiceLocation(ServiceLocation $serviceLocation): void
    {
        $existingPage = LocationPage::where('service_id', $serviceLocation->service_id)
            ->where('city_id', $serviceLocation->city_id)
            ->first();

        $serviceLocation->page_exists = $existingPage !== null;
        $serviceLocation->location_page_id = $existingPage?->id;

        if ($existingPage) {
            $this->updatePerformanceMetrics($serviceLocation, $existingPage);
        } else {
            $this->calculatePotentialMetrics($serviceLocation);
        }

        $serviceLocation->last_analyzed_at = now();
        $serviceLocation->save();
    }
}
