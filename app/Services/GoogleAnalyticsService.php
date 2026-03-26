<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    protected ?BetaAnalyticsDataClient $client = null;
    protected string $propertyId;

    public function __construct()
    {
        $this->propertyId = config('services.ga4.property_id', '');

        $credentialsPath = config('services.gsc.credentials_path');
        if ($credentialsPath && file_exists($credentialsPath)) {
            $this->client = new BetaAnalyticsDataClient([
                'credentials' => $credentialsPath,
            ]);
        }
    }

    /**
     * Fetch overview metrics for KPI cards.
     */
    public function fetchOverview(int $days = 28): array
    {
        $cacheKey = "ga4_overview_{$days}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($days) {
            try {
                $response = $this->client->runReport([
                    'property'   => "properties/{$this->propertyId}",
                    'dateRanges' => [new DateRange([
                        'start_date' => "{$days}daysAgo",
                        'end_date'   => 'yesterday',
                    ])],
                    'metrics' => [
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'totalUsers']),
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'bounceRate']),
                        new Metric(['name' => 'averageSessionDuration']),
                    ],
                ]);

                $row = $response->getRows()[0] ?? null;
                if (! $row) {
                    return $this->emptyOverview();
                }

                $values = $row->getMetricValues();
                return [
                    'sessions'          => (int) ($values[0]?->getValue() ?? 0),
                    'users'             => (int) ($values[1]?->getValue() ?? 0),
                    'pageviews'         => (int) ($values[2]?->getValue() ?? 0),
                    'bounce_rate'       => round((float) ($values[3]?->getValue() ?? 0) * 100, 1),
                    'avg_session_duration' => round((float) ($values[4]?->getValue() ?? 0), 1),
                ];
            } catch (\Throwable $e) {
                Log::channel('seo')->error('GA4 overview fetch failed', ['error' => $e->getMessage()]);
                return $this->emptyOverview();
            }
        });
    }

    /**
     * Fetch traffic sources breakdown.
     */
    public function fetchTrafficSources(int $days = 28): array
    {
        $cacheKey = "ga4_sources_{$days}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($days) {
            try {
                $response = $this->client->runReport([
                    'property'   => "properties/{$this->propertyId}",
                    'dateRanges' => [new DateRange([
                        'start_date' => "{$days}daysAgo",
                        'end_date'   => 'yesterday',
                    ])],
                    'dimensions' => [new Dimension(['name' => 'sessionDefaultChannelGroup'])],
                    'metrics'    => [
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'totalUsers']),
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'bounceRate']),
                    ],
                ]);

                return collect($response->getRows())->map(function ($row) {
                    $dims = $row->getDimensionValues();
                    $vals = $row->getMetricValues();
                    return [
                        'source'      => $dims[0]?->getValue() ?? 'unknown',
                        'sessions'    => (int) ($vals[0]?->getValue() ?? 0),
                        'users'       => (int) ($vals[1]?->getValue() ?? 0),
                        'pageviews'   => (int) ($vals[2]?->getValue() ?? 0),
                        'bounce_rate' => round((float) ($vals[3]?->getValue() ?? 0) * 100, 1),
                    ];
                })->toArray();
            } catch (\Throwable $e) {
                Log::channel('seo')->error('GA4 traffic sources failed', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Fetch top pages by sessions.
     */
    public function fetchTopPages(int $days = 28, int $limit = 20): array
    {
        $cacheKey = "ga4_pages_{$days}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($days, $limit) {
            try {
                $response = $this->client->runReport([
                    'property'   => "properties/{$this->propertyId}",
                    'dateRanges' => [new DateRange([
                        'start_date' => "{$days}daysAgo",
                        'end_date'   => 'yesterday',
                    ])],
                    'dimensions' => [new Dimension(['name' => 'pagePath'])],
                    'metrics'    => [
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'screenPageViews']),
                    ],
                    'limit' => $limit,
                ]);

                return collect($response->getRows())->map(function ($row) {
                    $dims = $row->getDimensionValues();
                    $vals = $row->getMetricValues();
                    return [
                        'page'      => $dims[0]?->getValue() ?? '/',
                        'sessions'  => (int) ($vals[0]?->getValue() ?? 0),
                        'pageviews' => (int) ($vals[1]?->getValue() ?? 0),
                    ];
                })->toArray();
            } catch (\Throwable $e) {
                Log::channel('seo')->error('GA4 top pages failed', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Fetch sessions over time for line chart.
     */
    public function fetchSessionsSeries(int $days = 28): array
    {
        $cacheKey = "ga4_sessions_series_{$days}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($days) {
            try {
                $response = $this->client->runReport([
                    'property'   => "properties/{$this->propertyId}",
                    'dateRanges' => [new DateRange([
                        'start_date' => "{$days}daysAgo",
                        'end_date'   => 'yesterday',
                    ])],
                    'dimensions' => [new Dimension(['name' => 'date'])],
                    'metrics'    => [new Metric(['name' => 'sessions'])],
                ]);

                return collect($response->getRows())->map(function ($row) {
                    $dims = $row->getDimensionValues();
                    $vals = $row->getMetricValues();
                    return [
                        'date'     => $dims[0]?->getValue() ?? '',
                        'sessions' => (int) ($vals[0]?->getValue() ?? 0),
                    ];
                })->sortBy('date')->values()->toArray();
            } catch (\Throwable $e) {
                Log::channel('seo')->error('GA4 sessions series failed', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Calculate organic traffic percentage from sources.
     */
    public function getOrganicPercentage(int $days = 28): float
    {
        $sources = $this->fetchTrafficSources($days);
        $total = array_sum(array_column($sources, 'sessions'));
        if ($total === 0) {
            return 0;
        }

        $organic = collect($sources)
            ->where('source', 'Organic Search')
            ->sum('sessions');

        return round($organic / $total * 100, 1);
    }

    protected function emptyOverview(): array
    {
        return [
            'sessions' => 0, 'users' => 0, 'pageviews' => 0,
            'bounce_rate' => 0, 'avg_session_duration' => 0,
        ];
    }
}
