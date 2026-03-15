<?php

namespace App\Services;

use App\Models\PerformanceMetric;
use App\Models\Site;
use App\Models\Page;
use App\Models\LocationPage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PerformanceAggregationService
{
    /**
     * Get 30-day performance summary for a page
     */
    public function get30DaySummary($page): ?array
    {
        $endDate = now()->subDay();
        $startDate = $endDate->copy()->subDays(30);

        $query = PerformanceMetric::where('site_id', $page->site_id ?? null)
            ->whereBetween('date', [$startDate, $endDate]);

        // Filter by page type
        if ($page instanceof LocationPage) {
            $query->where('location_page_id', $page->id);
        } elseif ($page instanceof Page) {
            $query->where('page_id', $page->id);
        } else {
            return null;
        }

        $aggregate = $query->selectRaw('
                SUM(clicks) as total_clicks,
                SUM(impressions) as total_impressions,
                AVG(ctr) as avg_ctr,
                AVG(average_position) as avg_position
            ')
            ->first();

        if (!$aggregate || $aggregate->total_impressions === 0) {
            return null;
        }

        return [
            'clicks' => (int) $aggregate->total_clicks,
            'impressions' => (int) $aggregate->total_impressions,
            'ctr' => round((float) $aggregate->avg_ctr, 4),
            'avg_position' => round((float) $aggregate->avg_position, 2),
            'period' => '30d',
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }

    /**
     * Get performance trend (comparing two periods)
     */
    public function getTrend($page, int $days = 30): ?array
    {
        $currentPeriodEnd = now()->subDay();
        $currentPeriodStart = $currentPeriodEnd->copy()->subDays($days);
        
        $previousPeriodEnd = $currentPeriodStart->copy()->subDay();
        $previousPeriodStart = $previousPeriodEnd->copy()->subDays($days);

        $currentData = $this->getPeriodData($page, $currentPeriodStart, $currentPeriodEnd);
        $previousData = $this->getPeriodData($page, $previousPeriodStart, $previousPeriodEnd);

        if (!$currentData || !$previousData) {
            return null;
        }

        return [
            'current' => $currentData,
            'previous' => $previousData,
            'change' => [
                'clicks' => $currentData['clicks'] - $previousData['clicks'],
                'impressions' => $currentData['impressions'] - $previousData['impressions'],
                'ctr' => round($currentData['ctr'] - $previousData['ctr'], 4),
                'position' => round($currentData['avg_position'] - $previousData['avg_position'], 2),
            ],
            'change_pct' => [
                'clicks' => $previousData['clicks'] > 0 
                    ? round((($currentData['clicks'] - $previousData['clicks']) / $previousData['clicks']) * 100, 1)
                    : null,
                'impressions' => $previousData['impressions'] > 0
                    ? round((($currentData['impressions'] - $previousData['impressions']) / $previousData['impressions']) * 100, 1)
                    : null,
                'ctr' => $previousData['ctr'] > 0
                    ? round((($currentData['ctr'] - $previousData['ctr']) / $previousData['ctr']) * 100, 1)
                    : null,
            ],
        ];
    }

    /**
     * Get aggregate data for a specific period
     */
    protected function getPeriodData($page, Carbon $startDate, Carbon $endDate): ?array
    {
        $query = PerformanceMetric::where('site_id', $page->site_id ?? null)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($page instanceof LocationPage) {
            $query->where('location_page_id', $page->id);
        } elseif ($page instanceof Page) {
            $query->where('page_id', $page->id);
        } else {
            return null;
        }

        $aggregate = $query->selectRaw('
                SUM(clicks) as total_clicks,
                SUM(impressions) as total_impressions,
                AVG(ctr) as avg_ctr,
                AVG(average_position) as avg_position
            ')
            ->first();

        if (!$aggregate) {
            return null;
        }

        return [
            'clicks' => (int) $aggregate->total_clicks,
            'impressions' => (int) $aggregate->total_impressions,
            'ctr' => round((float) $aggregate->avg_ctr, 4),
            'avg_position' => round((float) $aggregate->avg_position, 2),
        ];
    }

    /**
     * Get top performing queries for a page
     */
    public function getTopQueries($page, int $limit = 10): array
    {
        $query = PerformanceMetric::where('site_id', $page->site_id ?? null)
            ->whereNotNull('query')
            ->whereBetween('date', [now()->subDays(30), now()]);

        if ($page instanceof LocationPage) {
            $query->where('location_page_id', $page->id);
        } elseif ($page instanceof Page) {
            $query->where('page_id', $page->id);
        } else {
            return [];
        }

        return $query->selectRaw('
                query,
                SUM(clicks) as total_clicks,
                SUM(impressions) as total_impressions,
                AVG(ctr) as avg_ctr,
                AVG(average_position) as avg_position
            ')
            ->groupBy('query')
            ->orderByDesc('total_clicks')
            ->limit($limit)
            ->get()
            ->map(fn($row) => [
                'query' => $row->query,
                'clicks' => (int) $row->total_clicks,
                'impressions' => (int) $row->total_impressions,
                'ctr' => round((float) $row->avg_ctr, 4),
                'avg_position' => round((float) $row->avg_position, 2),
            ])
            ->toArray();
    }

    /**
     * Get site-wide summary
     */
    public function getSiteSummary(Site $site, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $aggregate = PerformanceMetric::where('site_id', $site->id)
            ->where('date', '>=', $startDate)
            ->selectRaw('
                SUM(clicks) as total_clicks,
                SUM(impressions) as total_impressions,
                AVG(ctr) as avg_ctr,
                AVG(average_position) as avg_position,
                COUNT(DISTINCT url) as unique_urls
            ')
            ->first();

        return [
            'clicks' => (int) ($aggregate->total_clicks ?? 0),
            'impressions' => (int) ($aggregate->total_impressions ?? 0),
            'ctr' => round((float) ($aggregate->avg_ctr ?? 0), 4),
            'avg_position' => round((float) ($aggregate->avg_position ?? 0), 2),
            'unique_urls' => (int) ($aggregate->unique_urls ?? 0),
            'period_days' => $days,
        ];
    }
}
