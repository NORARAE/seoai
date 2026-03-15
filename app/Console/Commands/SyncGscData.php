<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\GscSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncGscData extends Command
{
    protected $signature = 'gsc:sync 
                            {--site= : Sync specific site domain only}
                            {--days=30 : Number of days to sync}
                            {--force : Force sync even if recently synced}';

    protected $description = 'Sync Google Search Console performance data';

    public function handle(GscSyncService $gscService): int
    {
        $siteDomain = $this->option('site');
        $days = (int) $this->option('days');
        $force = $this->option('force');

        if ($siteDomain) {
            $site = Site::where('domain', $siteDomain)->first();

            if (!$site) {
                $this->error("Site not found: {$siteDomain}");
                return self::FAILURE;
            }

            $sites = collect([$site]);
        } else {
            // Get all sites connected to GSC
            $sites = Site::whereNotNull('gsc_property_url')
                ->whereNotNull('gsc_access_token')
                ->get();
        }

        if ($sites->isEmpty()) {
            $this->warn('No sites connected to Google Search Console.');
            return self::SUCCESS;
        }

        $this->info("Syncing {$sites->count()} site(s)...");

        $startDate = now()->subDays($days);
        $endDate = now()->subDay(); // GSC data has ~2 day delay

        foreach ($sites as $site) {
            // Skip if recently synced (unless forced)
            if (!$force && $site->gsc_last_sync_at && $site->gsc_last_sync_at->isToday()) {
                $this->line("⏭️  Skipping {$site->domain} (already synced today)");
                continue;
            }

            $this->line("📊 Syncing {$site->domain}...");

            $result = $gscService->syncSite($site, $startDate, $endDate);

            if ($result['success']) {
                $pageMetrics = $result['page_metrics'] ?? 0;
                $queryMetrics = $result['query_metrics'] ?? 0;
                
                $this->info("   ✓ {$site->domain}: {$pageMetrics} page metrics, {$queryMetrics} query metrics imported");
            } else {
                $this->error("   ✗ {$site->domain}: {$result['error']}");
            }
        }

        $this->info('✓ GSC sync completed');

        return self::SUCCESS;
    }
}
