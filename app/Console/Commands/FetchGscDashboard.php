<?php

namespace App\Console\Commands;

use App\Models\SeoKeyword;
use App\Models\SeoReport;
use App\Services\GoogleSearchConsoleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchGscDashboard extends Command
{
    protected $signature = 'seo:fetch-gsc {--days=28 : Number of days to fetch}';

    protected $description = 'Fetch GSC data and store in seo_reports / seo_keywords tables';

    public function handle(GoogleSearchConsoleService $gsc): int
    {
        $days = (int) $this->option('days');

        $this->info("Fetching GSC data for last {$days} days…");

        try {
            // --- Totals ---
            $totals = $gsc->fetchTotals($days);
            SeoReport::updateOrCreate(
                ['report_type' => 'gsc', 'dimension' => 'totals', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $totals, 'fetched_at' => now()],
            );
            $this->line('  ✓ Totals stored');

            // --- Date series ---
            $series = $gsc->fetchDateSeries($days);
            SeoReport::updateOrCreate(
                ['report_type' => 'gsc', 'dimension' => 'date', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $series, 'fetched_at' => now()],
            );
            $this->line('  ✓ Date series stored');

            // --- Top queries ---
            $queries = $gsc->fetchSearchAnalytics('query', $days, 50);
            SeoReport::updateOrCreate(
                ['report_type' => 'gsc', 'dimension' => 'query', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $queries, 'fetched_at' => now()],
            );

            // Upsert individual keywords
            foreach ($queries as $row) {
                SeoKeyword::updateOrCreate(
                    ['query' => $row['keys'][0] ?? $row['query'] ?? '', 'date_range' => "{$days}d"],
                    [
                        'clicks'      => $row['clicks'] ?? 0,
                        'impressions' => $row['impressions'] ?? 0,
                        'ctr'         => $row['ctr'] ?? 0,
                        'position'    => $row['position'] ?? 0,
                        'fetched_at'  => now(),
                    ],
                );
            }
            $this->line('  ✓ Keywords stored (' . count($queries) . ')');

            // --- Top pages ---
            $pages = $gsc->fetchSearchAnalytics('page', $days, 50);
            SeoReport::updateOrCreate(
                ['report_type' => 'gsc', 'dimension' => 'page', 'date_range' => "{$days}d"],
                ['site_url' => config('services.gsc.site_url', ''), 'data' => $pages, 'fetched_at' => now()],
            );
            $this->line('  ✓ Top pages stored (' . count($pages) . ')');

            $this->info('✓ GSC fetch complete');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::channel('seo')->error('seo:fetch-gsc failed', ['error' => $e->getMessage()]);
            $this->error('Failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
