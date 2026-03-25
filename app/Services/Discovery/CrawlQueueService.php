<?php

namespace App\Services\Discovery;

use App\Models\CrawlQueue;
use App\Models\InternalLink;
use App\Models\PageContent;
use App\Models\PageMetadata;
use App\Models\ScanRun;
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\SiteCrawlSetting;
use App\Models\UrlInventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrawlQueueService
{
    protected int $dispatchClaimSeconds = 90;

    public function __construct(
        protected UrlNormalizer $normalizer,
        protected RobotsPolicyService $robotsPolicyService,
        protected PageExtractionService $pageExtractionService,
    ) {}

    // ──────────────────────────────────────────────
    // Enqueue
    // ──────────────────────────────────────────────

    public function enqueueUrl(
        Site $site,
        string $url,
        string $discoveryMethod = 'crawl',
        int $depth = 0,
        ?UrlInventory $discoveredFrom = null,
        int $priority = 50,
        ?int $scanRunId = null,
    ): bool {
        $normalized = $this->normalizer->normalize($url);

        if ($scanRunId !== null) {
            $scanRun = ScanRun::query()->find($scanRunId);

            if (! $scanRun || ! in_array($scanRun->status, ['running', 'pending'], true)) {
                Log::warning('Late enqueue blocked because scan run is no longer active', [
                    'site_id' => $site->id,
                    'scan_run_id' => $scanRunId,
                    'discovery_method' => $discoveryMethod,
                    'url' => $url,
                    'scan_run_status' => $scanRun?->status,
                ]);

                return false;
            }
        }

        if (blank($normalized['normalized_url'])) {
            return false;
        }

        if (! $this->normalizer->isInternal($normalized['normalized_url'], $site->domain)) {
            return false;
        }

        $settings = SiteCrawlSetting::firstOrCreate(
            ['site_id' => $site->id],
            [
                'max_pages' => 2000,
                'crawl_delay' => 1,
                'max_depth' => 4,
                'obey_robots' => true,
                'follow_nofollow' => false,
            ],
        );

        if ($depth > $settings->max_depth) {
            return false;
        }

        $inventoryCount = UrlInventory::where('site_id', $site->id)->count();

        if ($inventoryCount >= $settings->max_pages) {
            return false;
        }

        return DB::transaction(function () use ($site, $normalized, $depth, $discoveryMethod, $discoveredFrom, $priority, $scanRunId): bool {
            $inventory = UrlInventory::firstOrCreate(
                [
                    'site_id' => $site->id,
                    'normalized_url' => $normalized['normalized_url'],
                ],
                [
                    'url' => $normalized['normalized_url'],
                    'path' => $normalized['path'],
                    'depth' => $depth,
                    'discovered_from' => $discoveredFrom?->id,
                    'discovery_method' => $discoveryMethod,
                    'status' => 'queued',
                    'crawl_priority' => $priority,
                    'page_type' => $this->classifyPageType($normalized['path']),
                    'first_seen_scan_run_id' => $scanRunId,
                    'last_seen_scan_run_id' => $scanRunId,
                ],
            );

            if ($inventory->depth > $depth) {
                $inventory->update(['depth' => $depth]);
            }

            // On re-encounter: stamp last_seen even if the URL already existed.
            if (! $inventory->wasRecentlyCreated && $scanRunId !== null) {
                $inventory->timestamps = false;
                $inventory->update(['last_seen_scan_run_id' => $scanRunId]);
                $inventory->timestamps = true;
            }

            $existingQueued = CrawlQueue::where('site_id', $site->id)
                ->where('url_inventory_id', $inventory->id)
                ->whereIn('status', ['queued', 'processing'])
                ->exists();

            if ($existingQueued) {
                return false;
            }

            $queueItem = CrawlQueue::create([
                'site_id' => $site->id,
                'scan_run_id' => $scanRunId,
                'url_inventory_id' => $inventory->id,
                'url' => $inventory->normalized_url,
                'priority' => $priority,
                'depth' => $depth,
                'status' => 'queued',
                'attempts' => 0,
                'discovered_from' => $discoveredFrom?->normalized_url,
                'available_at' => now(),
            ]);

            Log::info('Scan run enqueue created', [
                'site_id' => $site->id,
                'scan_run_id' => $scanRunId,
                'queue_id' => $queueItem->id,
                'url_inventory_id' => $inventory->id,
                'url' => $queueItem->url,
                'depth' => $depth,
                'discovery_method' => $discoveryMethod,
            ]);

            return true;
        });
    }

    // ──────────────────────────────────────────────
    // Dispatch helpers
    // ──────────────────────────────────────────────

    public function getDispatchableItems(?int $siteId = null, int $limit = 50)
    {
        return CrawlQueue::query()
            ->when($siteId, fn ($query) => $query->where('site_id', $siteId))
            ->where('status', 'queued')
            ->where(function ($query): void {
                $query->whereNull('available_at')
                    ->orWhere('available_at', '<=', now());
            })
            ->orderByDesc('priority')
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function claimDispatchableItems(?int $siteId = null, int $limit = 50): Collection
    {
        return DB::transaction(function () use ($siteId, $limit): Collection {
            $items = $this->getDispatchableItems($siteId, $limit);

            if ($items->isEmpty()) {
                return new Collection();
            }

            CrawlQueue::query()
                ->whereIn('id', $items->pluck('id'))
                ->update([
                    'available_at' => now()->addSeconds($this->dispatchClaimSeconds),
                    'updated_at' => now(),
                ]);

            return CrawlQueue::query()
                ->with(['site', 'urlInventory'])
                ->whereIn('id', $items->pluck('id'))
                ->orderByDesc('priority')
                ->orderBy('id')
                ->get();
        });
    }

    // ──────────────────────────────────────────────
    // Site crawl status + ScanRun finalization
    // ──────────────────────────────────────────────

    public function refreshSiteCrawlStatus(Site|int $site): void
    {
        $siteModel = $site instanceof Site ? $site : Site::find($site);

        if (! $siteModel) {
            return;
        }

        $counts = CrawlQueue::query()
            ->where('site_id', $siteModel->id)
            ->selectRaw("SUM(CASE WHEN status = 'queued' THEN 1 ELSE 0 END) as queued_count")
            ->selectRaw("SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_count")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count")
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count")
            ->first();

        $queuedCount = (int) ($counts?->queued_count ?? 0);
        $processingCount = (int) ($counts?->processing_count ?? 0);
        $completedCount = (int) ($counts?->completed_count ?? 0);
        $failedCount = (int) ($counts?->failed_count ?? 0);
        $hasQueueHistory = ($completedCount + $failedCount + $queuedCount + $processingCount) > 0;

        $status = 'idle';

        if (($queuedCount + $processingCount) > 0) {
            $status = 'processing';
        } elseif ($hasQueueHistory) {
            $status = 'completed';
        }

        $pagesCrawled = UrlInventory::query()
            ->where('site_id', $siteModel->id)
            ->where('status', 'completed')
            ->count();

        $lastCrawledAt = UrlInventory::query()
            ->where('site_id', $siteModel->id)
            ->max('last_crawled_at');

        $siteModel->forceFill([
            'crawl_status' => $status,
            'pages_crawled' => $pagesCrawled,
            'last_crawled_at' => $lastCrawledAt,
        ])->save();

        // Scan completion is run-scoped, not site-scoped.
        $this->finalizeScanRunIfRunning($siteModel->id);
    }

    /**
     * Look up the most recent running ScanRun for a site and mark it completed
     * with denormalized summary counts. Idempotent via ScanRun::markCompleted().
     */
    protected function finalizeScanRunIfRunning(int $siteId): void
    {
        $scanRun = ScanRun::where('site_id', $siteId)
            ->whereIn('status', ['running', 'pending'])
            ->latest('started_at')
            ->first();

        if (! $scanRun) {
            return;
        }

        $queued = CrawlQueue::where('scan_run_id', $scanRun->id)
            ->where('status', 'queued')
            ->count();

        $processing = CrawlQueue::where('scan_run_id', $scanRun->id)
            ->where('status', 'processing')
            ->count();

        Log::info('Scan run completion trigger evaluated', [
            'site_id' => $siteId,
            'scan_run_id' => $scanRun->id,
            'queued' => $queued,
            'processing' => $processing,
        ]);

        if (($queued + $processing) > 0) {
            return;
        }

        $discovered = CrawlQueue::where('scan_run_id', $scanRun->id)
            ->whereNotNull('url_inventory_id')
            ->distinct('url_inventory_id')
            ->count('url_inventory_id');

        $crawled = CrawlQueue::where('scan_run_id', $scanRun->id)
            ->where('status', 'completed')
            ->whereNotNull('url_inventory_id')
            ->distinct('url_inventory_id')
            ->count('url_inventory_id');

        $failed = CrawlQueue::where('scan_run_id', $scanRun->id)
            ->where('status', 'failed')
            ->whereNotNull('url_inventory_id')
            ->distinct('url_inventory_id')
            ->count('url_inventory_id');

        $opportunities = SeoOpportunity::where('site_id', $siteId)
            ->where('scan_run_id', $scanRun->id)
            ->count();

        Log::info('Scan run summary calculated', [
            'site_id' => $siteId,
            'scan_run_id' => $scanRun->id,
            'pages_discovered' => $discovered,
            'pages_crawled' => $crawled,
            'pages_failed' => $failed,
            'opportunities_found' => $opportunities,
        ]);

        $scanRun->markCompleted($discovered, $crawled, $failed, $opportunities);

        Log::info('ScanRun finalized', [
            'site_id'          => $siteId,
            'scan_run_id'      => $scanRun->id,
            'pages_discovered' => $discovered,
            'pages_crawled'    => $crawled,
            'pages_failed'     => $failed,
            'opportunities'    => $opportunities,
        ]);
    }

    // ──────────────────────────────────────────────
    // Queue backlog helpers
    // ──────────────────────────────────────────────

    public function hasQueuedBacklog(int $siteId): bool
    {
        return CrawlQueue::query()
            ->where('site_id', $siteId)
            ->where('status', 'queued')
            ->exists();
    }

    public function hasReadyQueuedItems(int $siteId): bool
    {
        return CrawlQueue::query()
            ->where('site_id', $siteId)
            ->where('status', 'queued')
            ->where(function ($query): void {
                $query->whereNull('available_at')
                    ->orWhere('available_at', '<=', now());
            })
            ->exists();
    }

    public function nextDispatchDelay(int $siteId): ?int
    {
        if (! $this->hasQueuedBacklog($siteId)) {
            return null;
        }

        $nextAvailableAt = CrawlQueue::query()
            ->where('site_id', $siteId)
            ->where('status', 'queued')
            ->min('available_at');

        if (! $nextAvailableAt) {
            return 0;
        }

        $nextAvailable = Carbon::parse($nextAvailableAt);

        if ($nextAvailable->lte(now())) {
            return 0;
        }

        return now()->diffInSeconds($nextAvailable);
    }

    // ──────────────────────────────────────────────
    // Item processing
    // ──────────────────────────────────────────────

    public function processQueueItem(CrawlQueue $item): array
    {
        $site = $item->site;

        if (! $site) {
            return ['status' => 'failed', 'reason' => 'Site missing'];
        }

        $item->update([
            'status' => 'processing',
            'attempts' => $item->attempts + 1,
            'last_attempted_at' => now(),
            'available_at' => null,
        ]);

        if (! $this->robotsPolicyService->canRequestNow($site)) {
            $delay = $this->robotsPolicyService->getEffectiveDelay($site);
            $item->update([
                'status' => 'queued',
                'available_at' => now()->addSeconds($delay),
            ]);

            return ['status' => 'delayed', 'reason' => 'Rate-limited'];
        }

        if (! $this->robotsPolicyService->isAllowed($site, $item->url)) {
            $this->markBlocked($item);

            return ['status' => 'blocked', 'reason' => 'Blocked by robots'];
        }

        try {
            $response = Http::timeout(30)
                ->connectTimeout(10)
                ->withUserAgent('SEOAIco/1.0 (SEO Crawler)')
                ->get($item->url);

            $this->robotsPolicyService->touchLastRequest($site);

            if (in_array($response->status(), [429, 503], true)) {
                $delay = $this->robotsPolicyService->backoffDelay($site);

                return $this->retryOrFail($item, "HTTP {$response->status()} rate limit", $delay);
            }

            if ($this->isNonRetriableHttpStatus($response->status())) {
                $item->update([
                    'status' => 'failed',
                    'error_message' => "HTTP {$response->status()} fetch failed",
                ]);

                if ($item->url_inventory_id) {
                    UrlInventory::whereKey($item->url_inventory_id)->update([
                        'status' => 'failed',
                        'indexability_status' => 'non_200',
                    ]);
                }

                $this->refreshSiteCrawlStatus($item->site_id);

                return ['status' => 'failed', 'reason' => "HTTP {$response->status()} fetch failed"];
            }

            if (! $response->successful()) {
                return $this->retryOrFail($item, "HTTP {$response->status()} fetch failed", $this->attemptDelay($item->attempts));
            }

            $this->storeExtractionResult($item, $response->body(), $response->status());

            $item->update(['status' => 'completed', 'error_message' => null]);

            $this->refreshSiteCrawlStatus($site);

            return ['status' => 'completed'];
        } catch (\Throwable $exception) {
            $error = $exception->getMessage();

            if ($this->isTimeoutError($error)) {
                return $this->retryOrFail($item, $error, $this->timeoutAttemptDelay($item->attempts));
            }

            return $this->retryOrFail($item, $error, $this->attemptDelay($item->attempts));
        }
    }

    protected function retryOrFail(CrawlQueue $item, string $error, int $delay): array
    {
        if ($item->attempts >= 3) {
            $item->update([
                'status' => 'failed',
                'error_message' => $error,
            ]);

            if ($item->url_inventory_id) {
                UrlInventory::whereKey($item->url_inventory_id)->update([
                    'status' => 'failed',
                    'indexability_status' => 'non_200',
                ]);
            }

            $this->refreshSiteCrawlStatus($item->site_id);

            return ['status' => 'failed', 'reason' => $error];
        }

        $item->update([
            'status' => 'queued',
            'error_message' => $error,
            'available_at' => now()->addSeconds($delay),
        ]);

        $this->refreshSiteCrawlStatus($item->site_id);

        return ['status' => 'retrying', 'reason' => $error];
    }

    protected function markBlocked(CrawlQueue $item): void
    {
        $item->update([
            'status' => 'completed',
            'error_message' => 'Blocked by robots.txt',
        ]);

        if ($item->url_inventory_id) {
            UrlInventory::whereKey($item->url_inventory_id)->update([
                'status' => 'completed',
                'indexability_status' => 'blocked',
                'last_crawled_at' => now(),
            ]);
        }

        $this->refreshSiteCrawlStatus($item->site_id);
    }

    // ──────────────────────────────────────────────
    // Extraction + link storage
    // ──────────────────────────────────────────────

    protected function storeExtractionResult(CrawlQueue $item, string $html, int $statusCode): void
    {
        $site = $item->site;
        $inventory = $item->urlInventory;
        $scanRunId = $item->scan_run_id;

        if (! $site || ! $inventory) {
            return;
        }

        $extracted = $this->pageExtractionService->extract($html, $inventory->normalized_url);
        $contentHash = hash('sha256', trim($extracted['body_text']));
        $indexability = $this->determineIndexability($inventory->normalized_url, $statusCode, $extracted['meta_robots'], $extracted['canonical']);

        $inventory->update([
            'status' => 'completed',
            'last_crawled_at' => now(),
            'content_hash' => $contentHash,
            'word_count' => $extracted['word_count'],
            'indexability_status' => $indexability,
            'page_type' => $this->classifyPageType($inventory->path ?? '/'),
            'last_seen_scan_run_id' => $scanRunId ?? $inventory->last_seen_scan_run_id,
        ]);

        PageMetadata::updateOrCreate(
            ['url_id' => $inventory->id],
            [
                'title' => $extracted['title'],
                'meta_description' => $extracted['meta_description'],
                'canonical' => $extracted['canonical'],
                'h1' => $extracted['h1'],
                'h2s' => $extracted['h2s'],
                'meta_robots' => $extracted['meta_robots'],
                'schema' => $extracted['schema'],
            ],
        );

        PageContent::updateOrCreate(
            ['url_id' => $inventory->id],
            [
                'body_text' => $extracted['body_text'],
                'excerpt' => $extracted['excerpt'],
                'word_count' => $extracted['word_count'],
                'readability' => $extracted['readability'],
            ],
        );

        $latestSnapshot = DB::table('page_snapshots')
            ->where('url_id', $inventory->id)
            ->orderByDesc('snapshot_date')
            ->first();

        if (! $latestSnapshot || $latestSnapshot->content_hash !== $contentHash) {
            DB::table('page_snapshots')->insert([
                'url_id' => $inventory->id,
                'content_hash' => $contentHash,
                'snapshot_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->storeDiscoveredLinks($site, $inventory, $extracted['links'], $item->depth + 1, $scanRunId);
    }

    protected function storeDiscoveredLinks(Site $site, UrlInventory $source, array $links, int $nextDepth, ?int $scanRunId = null): void
    {
        $followNofollow = (bool) SiteCrawlSetting::where('site_id', $site->id)
            ->value('follow_nofollow');

        // Track which targets we've incremented incoming_link_count for this batch.
        $countedTargetIds = [];

        foreach ($links as $link) {
            $rel = strtolower((string) ($link['rel'] ?? ''));

            if (! $followNofollow && str_contains($rel, 'nofollow')) {
                continue;
            }

            $resolved = $this->normalizer->resolveUrl($link['url'], $source->normalized_url);

            if (! $resolved || ! $this->normalizer->isInternal($resolved, $site->domain)) {
                continue;
            }

            $this->enqueueUrl($site, $resolved, 'crawl', $nextDepth, $source, $this->derivePriority($resolved), $scanRunId);

            $normalizedTarget = $this->normalizer->normalize($resolved)['normalized_url'];

            $target = UrlInventory::where('site_id', $site->id)
                ->where('normalized_url', $normalizedTarget)
                ->first();

            if (! $target) {
                continue;
            }

            // Skip self-links.
            if ($target->id === $source->id) {
                continue;
            }

            // Deduplicate by source→target edge only (anchor_text variations are not separate edges).
            $wasNew = InternalLink::updateOrCreate(
                [
                    'site_id'        => $site->id,
                    'source_url_id'  => $source->id,
                    'target_url_id'  => $target->id,
                ],
                [
                    'source_url'  => $source->normalized_url,
                    'target_url'  => $target->normalized_url,
                    'anchor_text' => $link['anchor_text'],
                ],
            )->wasRecentlyCreated;

            if ($wasNew) {
                // Increment outbound count on source and inbound count on target.
                UrlInventory::whereKey($source->id)->increment('internal_link_count');

                if (! in_array($target->id, $countedTargetIds, true)) {
                    $countedTargetIds[] = $target->id;
                    UrlInventory::whereKey($target->id)->increment('incoming_link_count');
                    // Once the target has an incoming link it is no longer an orphan.
                    UrlInventory::whereKey($target->id)->where('is_orphan_page', true)
                        ->update(['is_orphan_page' => false]);
                }
            }
        }
    }

    // ──────────────────────────────────────────────
    // Utility helpers
    // ──────────────────────────────────────────────

    protected function determineIndexability(string $url, int $statusCode, ?string $metaRobots, ?string $canonical): string
    {
        if ($statusCode !== 200) {
            return 'non_200';
        }

        if ($metaRobots && str_contains(strtolower($metaRobots), 'noindex')) {
            return 'noindex';
        }

        if ($canonical && $this->normalizer->normalize($canonical)['normalized_url'] !== $this->normalizer->normalize($url)['normalized_url']) {
            return 'canonicalized';
        }

        return 'indexable';
    }

    protected function attemptDelay(int $attempt): int
    {
        return match ($attempt) {
            1 => 60,
            2 => 300,
            default => 900,
        };
    }

    protected function timeoutAttemptDelay(int $attempt): int
    {
        return match ($attempt) {
            1 => 120,
            2 => 600,
            default => 1800,
        };
    }

    protected function isTimeoutError(string $error): bool
    {
        $normalized = strtolower($error);

        return str_contains($normalized, 'curl error 28')
            || str_contains($normalized, 'operation timed out')
            || str_contains($normalized, 'timed out');
    }

    protected function isNonRetriableHttpStatus(int $statusCode): bool
    {
        return in_array($statusCode, [400, 401, 403, 404, 410, 422], true);
    }

    protected function classifyPageType(string $path): string
    {
        if ($path === '/' || $path === '') {
            return 'homepage';
        }

        $path = strtolower($path);

        if (str_contains($path, '/category') || str_contains($path, '/services')) {
            return 'category';
        }

        if (str_contains($path, '/service') || str_contains($path, '-cleanup')) {
            return 'service';
        }

        if (preg_match('/\b(city|county|state|wa|or|id|mt|spokane|seattle|tacoma)\b/', $path)) {
            return 'location';
        }

        if (str_contains($path, '/blog') || str_contains($path, '/news')) {
            return 'blog';
        }

        if (str_contains($path, '/landing') || str_contains($path, '/lp/')) {
            return 'landing';
        }

        return 'other';
    }

    protected function derivePriority(string $url): int
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        if ($path === '/') {
            return 100;
        }

        if (str_contains($path, '/category') || str_contains($path, '/services')) {
            return 90;
        }

        if (str_contains($path, '/service')) {
            return 80;
        }

        return 50;
    }
}
