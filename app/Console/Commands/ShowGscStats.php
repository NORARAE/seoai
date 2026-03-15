<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\PerformanceAggregationService;
use Illuminate\Console\Command;

class ShowGscStats extends Command
{
    protected $signature = 'gsc:stats 
                            {--site= : Show stats for specific site domain}
                            {--days=30 : Number of days to analyze}';

    protected $description = 'Show Google Search Console performance statistics';

    public function handle(PerformanceAggregationService $perfService): int
    {
        $siteDomain = $this->option('site');
        $days = (int) $this->option('days');

        if ($siteDomain) {
            $site = Site::where('domain', $siteDomain)->first();

            if (!$site) {
                $this->error("Site not found: {$siteDomain}");
                return self::FAILURE;
            }

            $sites = collect([$site]);
        } else {
            $sites = Site::whereNotNull('gsc_property_url')->get();
        }

        if ($sites->isEmpty()) {
            $this->warn('No sites connected to Google Search Console.');
            return self::SUCCESS;
        }

        foreach ($sites as $site) {
            $this->info("📊 {$site->domain}");
            $this->line(str_repeat('─', 60));

            $summary = $perfService->getSiteSummary($site, $days);

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Clicks', number_format($summary['clicks'])],
                    ['Total Impressions', number_format($summary['impressions'])],
                    ['Average CTR', round($summary['ctr'] * 100, 2) . '%'],
                    ['Average Position', round($summary['avg_position'], 2)],
                    ['Unique URLs', number_format($summary['unique_urls'])],
                    ['Period', "{$days} days"],
                    ['Last Sync', $site->gsc_last_sync_at?->diffForHumans() ?? 'Never'],
                ]
            );

            $this->newLine();
        }

        return self::SUCCESS;
    }
}
