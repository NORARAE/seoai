<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\SearchConsole;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleService
{
    protected GoogleClient $client;
    protected SearchConsole $searchConsole;
    protected string $siteUrl;

    public function __construct()
    {
        $this->siteUrl = config('services.gsc.site_url', 'https://seoaico.com/');

        $this->client = new GoogleClient();
        $this->client->setApplicationName(config('app.name') . ' GSC');
        $this->client->setScopes([SearchConsole::WEBMASTERS_READONLY]);

        $credentialsPath = config('services.gsc.credentials_path');
        if ($credentialsPath && file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
        }

        $this->searchConsole = new SearchConsole($this->client);
    }

    /**
     * Fetch search analytics data from GSC.
     *
     * @param  string  $dimension  query|page|device|country
     * @param  int     $days       7|28|90
     */
    public function fetchSearchAnalytics(string $dimension = 'query', int $days = 28, int $rowLimit = 100): array
    {
        $cacheKey = "gsc_{$dimension}_{$days}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($dimension, $days, $rowLimit) {
            return $this->querySearchAnalytics($dimension, $days, $rowLimit);
        });
    }

    /**
     * Fetch date-level data for time-series charts.
     */
    public function fetchDateSeries(int $days = 28): array
    {
        $cacheKey = "gsc_date_{$days}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($days) {
            return $this->querySearchAnalytics('date', $days, 5000);
        });
    }

    /**
     * Get aggregated totals for KPI cards.
     */
    public function fetchTotals(int $days = 28): array
    {
        $cacheKey = "gsc_totals_{$days}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($days) {
            try {
                $request = new SearchAnalyticsQueryRequest();
                $request->setStartDate(now()->subDays($days)->toDateString());
                $request->setEndDate(now()->subDay()->toDateString());

                $response = $this->searchConsole->searchanalytics->query($this->siteUrl, $request);
                $rows = $response->getRows();

                if (empty($rows)) {
                    return ['clicks' => 0, 'impressions' => 0, 'ctr' => 0, 'position' => 0];
                }

                $totalClicks = 0;
                $totalImpressions = 0;
                $totalCtr = 0;
                $totalPosition = 0;
                $count = count($rows);

                foreach ($rows as $row) {
                    $totalClicks += $row->getClicks();
                    $totalImpressions += $row->getImpressions();
                    $totalCtr += $row->getCtr();
                    $totalPosition += $row->getPosition();
                }

                return [
                    'clicks'      => $totalClicks,
                    'impressions' => $totalImpressions,
                    'ctr'         => $count > 0 ? round($totalCtr / $count * 100, 2) : 0,
                    'position'    => $count > 0 ? round($totalPosition / $count, 1) : 0,
                ];
            } catch (\Throwable $e) {
                Log::channel('seo')->error('GSC totals fetch failed', ['error' => $e->getMessage()]);
                return ['clicks' => 0, 'impressions' => 0, 'ctr' => 0, 'position' => 0];
            }
        });
    }

    protected function querySearchAnalytics(string $dimension, int $days, int $rowLimit): array
    {
        try {
            $request = new SearchAnalyticsQueryRequest();
            $request->setStartDate(now()->subDays($days)->toDateString());
            $request->setEndDate(now()->subDay()->toDateString());
            $request->setDimensions([$dimension]);
            $request->setRowLimit($rowLimit);

            $response = $this->searchConsole->searchanalytics->query($this->siteUrl, $request);
            $rows = $response->getRows();

            return collect($rows)->map(function ($row) use ($dimension) {
                $keys = $row->getKeys();
                return [
                    $dimension    => $keys[0] ?? '',
                    'clicks'      => $row->getClicks(),
                    'impressions' => $row->getImpressions(),
                    'ctr'         => round($row->getCtr() * 100, 2),
                    'position'    => round($row->getPosition(), 1),
                ];
            })->toArray();
        } catch (\Throwable $e) {
            Log::channel('seo')->error("GSC query failed [{$dimension}]", [
                'days'  => $days,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
