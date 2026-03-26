<?php

namespace App\Console\Commands;

use App\Models\SeoReport;
use App\Models\SeoTraffic;
use App\Services\GoogleAnalyticsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchGa4Dashboard extends Command
{
    protected $signature = 'seo:fetch-ga4 {--days=28 : Number of days to fetch}';

    protected $description = 'Fetch GA4 data and store in seo_reports / seo_traffic tables';

    public function handle(GoogleAnalyticsService $ga4): int
    {
        $days = (int) $this->option('days');

        $this->info("Fetching GA4 data for last {$days} days…");

        try {
            // --- Overview ---
            $overview = $ga4->fetchOverview($days);
            SeoReport::updateOrCreate(
                ['report_type' => 'ga4', 'dimension' => 'overview', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $overview, 'fetched_at' => now()],
            );
            $this->line('  ✓ Overview stored');

            // --- Sessions series ---
            $sessions = $ga4->fetchSessionsSeries($days);
            SeoReport::updateOrCreate(
                ['report_type' => 'ga4', 'dimension' => 'sessions_series', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $sessions, 'fetched_at' => now()],
            );
            $this->line('  ✓ Sessions series stored');

            // --- Traffic sources ---
            $sources = $ga4->fetchTrafficSources($days);
            SeoReport::updateOrCreate(
                ['report_type' => 'ga4', 'dimension' => 'traffic_sources', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $sources, 'fetched_at' => now()],
            );

            foreach ($sources as $src) {
                SeoTraffic::updateOrCreate(
                    ['source' => $src['source'] ?? 'unknown', 'date_range' => "{$days}d"],
                    [
                        'sessions'    => $src['sessions'] ?? 0,
                        'users'       => $src['users'] ?? 0,
                        'pageviews'   => $src['pageviews'] ?? 0,
                        'bounce_rate' => $src['bounceRate'] ?? 0,
                        'fetched_at'  => now(),
                    ],
                );
            }
            $this->line('  ✓ Traffic sources stored (' . count($sources) . ')');

            // --- Top pages ---
            $pages = $ga4->fetchTopPages($days, 50);
            SeoReport::updateOrCreate(
                ['report_type' => 'ga4', 'dimension' => 'top_pages', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $pages, 'fetched_at' => now()],
            );
            $this->line('  ✓ Top pages stored (' . count($pages) . ')');

            // --- Organic % ---
            $organicPct = $ga4->getOrganicPercentage($days);
            SeoReport::updateOrCreate(
                ['report_type' => 'ga4', 'dimension' => 'organic_pct', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => ['organic_percentage' => $organicPct], 'fetched_at' => now()],
            );
            $this->line("  ✓ Organic % = {$organicPct}");

            $this->info('✓ GA4 fetch complete');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::channel('seo')->error('seo:fetch-ga4 failed', ['error' => $e->getMessage()]);
            $this->error('Failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
