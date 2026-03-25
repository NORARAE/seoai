<?php

namespace App\Jobs;

use App\Models\Site;
use App\Models\ScanRun;
use App\Services\Discovery\CrawlQueueService;
use App\Services\Discovery\RobotsPolicyService;
use App\Services\Discovery\SitemapIngestionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StartSiteDiscoveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    public function __construct(
        public int $siteId,
        public string $triggeredByType = 'scheduled',
        public ?int $initiatedBy = null,
    ) {}

    public function handle(
        RobotsPolicyService $robotsPolicyService,
        SitemapIngestionService $sitemapIngestionService,
        CrawlQueueService $crawlQueueService,
    ): void {
        $site = Site::findOrFail($this->siteId);

        $existingRun = ScanRun::where('site_id', $site->id)
            ->whereIn('status', ['running', 'pending'])
            ->latest('started_at')
            ->first();

        if ($existingRun) {
            Log::warning('Site discovery start skipped because an active scan run already exists', [
                'site_id' => $site->id,
                'domain' => $site->domain,
                'existing_scan_run_id' => $existingRun->id,
                'existing_status' => $existingRun->status,
            ]);

            return;
        }

        // Create a durable scan run record before doing any work.
        $scanRun = ScanRun::create([
            'site_id'           => $site->id,
            'triggered_by_type' => $this->triggeredByType,
            'initiated_by'      => $this->initiatedBy,
            'crawl_mode'        => 'full',
            'seed_source'       => 'homepage',
            'status'            => 'running',
            'started_at'        => now(),
        ]);

        $site->forceFill([
            'crawl_status' => 'processing',
            'last_crawled_at' => null,
        ])->save();

        $robotsPolicyService->refreshPolicy($site);
        $ingested = $site->sitemap_enabled
            ? $sitemapIngestionService->ingest($site, $scanRun->id)
            : ['discovered' => 0, 'queued' => 0, 'sitemap_count' => 0];

        $homepage = 'https://' . ltrim($site->domain, '/');
        $homepageQueued = $crawlQueueService->enqueueUrl($site, $homepage, 'crawl', 0, null, 100, $scanRun->id);
        $crawlQueueService->refreshSiteCrawlStatus($site);

        DispatchCrawlQueueJob::dispatch($site->id, 50)->onQueue('crawl');

        Log::info('Scan run started', [
            'site_id' => $site->id,
            'domain' => $site->domain,
            'scan_run_id' => $scanRun->id,
            'triggered_by_type' => $this->triggeredByType,
            'used_sitemap_seed' => $site->sitemap_enabled,
            'sitemap_discovered' => $ingested['discovered'],
            'sitemap_queued' => $ingested['queued'],
            'homepage_queued' => $homepageQueued,
            'initial_dispatch_limit' => 50,
        ]);
    }

    /**
     * Mark the scan run failed if this job itself throws before completing.
     */
    public function failed(\Throwable $exception): void
    {
        $scanRun = ScanRun::where('site_id', $this->siteId)
            ->where('status', 'running')
            ->latest('started_at')
            ->first();

        $scanRun?->markFailed(
            'StartSiteDiscoveryJob failed: ' . $exception->getMessage()
        );

        Log::error('StartSiteDiscoveryJob catastrophic failure', [
            'site_id'  => $this->siteId,
            'scan_run_id' => $scanRun?->id,
            'error'    => $exception->getMessage(),
        ]);
    }
}
