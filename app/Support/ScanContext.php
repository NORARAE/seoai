<?php

namespace App\Support;

use App\Models\CrawlQueue;
use App\Models\ScanRun;
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\SiteCrawlSetting;
use App\Models\UrlInventory;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;

final class ScanContext
{
    public function __construct(
        public readonly ?Site $site,
        public readonly ?ScanRun $metricsScan,
        public readonly ?ScanRun $activeScan,
        public readonly string $state,
        public readonly int $discovered,
        public readonly int $crawled,
        public readonly int $failed,
        public readonly int $limit,
        public readonly int $queued,
        public readonly int $processing,
        public readonly bool $isStalled,
        public readonly bool $isLimited,
        public readonly ?CarbonInterface $lastCompletedAt,
        public readonly ?CarbonInterface $lastActivityAt,
        public readonly int $opportunities,
    ) {}

    public static function forUser(?User $user, ?int $explicitSiteId = null, ?int $explicitScanRunId = null): self
    {
        $site = CurrentScanResolver::resolveSiteForUser($user, $explicitSiteId);
        $metricsScan = CurrentScanResolver::resolveForUser($user, $explicitSiteId, $explicitScanRunId);
        $activeScan = CurrentScanResolver::resolveActiveForUser($user, $explicitSiteId);

        if (! $site) {
            return new self(
                site: null,
                metricsScan: null,
                activeScan: null,
                state: 'idle',
                discovered: 0,
                crawled: 0,
                failed: 0,
                limit: 0,
                queued: 0,
                processing: 0,
                isStalled: false,
                isLimited: false,
                lastCompletedAt: null,
                lastActivityAt: null,
                opportunities: 0,
            );
        }

        $settings = SiteCrawlSetting::query()->firstOrCreate(
            ['site_id' => $site->id],
            [
                'max_pages' => 2000,
                'crawl_delay' => 1,
                'max_depth' => 4,
                'obey_robots' => true,
                'follow_nofollow' => false,
            ],
        );

        $discovered = $metricsScan
            ? UrlInventory::query()->where('site_id', $site->id)->where('last_seen_scan_run_id', $metricsScan->id)->count()
            : 0;

        $crawled = $metricsScan
            ? UrlInventory::query()->where('site_id', $site->id)->where('last_seen_scan_run_id', $metricsScan->id)->where('status', 'completed')->count()
            : 0;

        $failed = $metricsScan
            ? CrawlQueue::query()->where('scan_run_id', $metricsScan->id)->where('status', 'failed')->count()
            : 0;

        $opportunities = $metricsScan
            ? SeoOpportunity::query()->where('site_id', $site->id)->where('scan_run_id', $metricsScan->id)->count()
            : 0;

        $siteQueued = CrawlQueue::query()->where('site_id', $site->id)->where('status', 'queued')->count();
        $siteProcessing = CrawlQueue::query()->where('site_id', $site->id)->where('status', 'processing')->count();

        $queued = $activeScan
            ? CrawlQueue::query()->where('scan_run_id', $activeScan->id)->where('status', 'queued')->count()
            : 0;

        $processing = $activeScan
            ? CrawlQueue::query()->where('scan_run_id', $activeScan->id)->where('status', 'processing')->count()
            : 0;

        $scanRequested = Cache::has("site-scan-requested:{$site->id}");

        if (! $activeScan && ($scanRequested || $site->crawl_status === 'processing')) {
            $queued = $siteQueued;
            $processing = $siteProcessing;
        }

        $latestAttemptedAt = $activeScan
            ? CrawlQueue::query()->where('scan_run_id', $activeScan->id)->max('last_attempted_at')
            : CrawlQueue::query()->where('site_id', $site->id)->max('last_attempted_at');

        $latestUpdatedAt = $activeScan
            ? CrawlQueue::query()->where('scan_run_id', $activeScan->id)->max('updated_at')
            : CrawlQueue::query()->where('site_id', $site->id)->max('updated_at');

        $lastActivityAt = collect([$latestAttemptedAt, $latestUpdatedAt])
            ->filter()
            ->map(fn ($value) => Carbon::parse((string) $value))
            ->sortDesc()
            ->first();

        $isStalled = $activeScan !== null
            ? $queued > 0
                && $processing === 0
                && $lastActivityAt !== null
                && now()->diffInMinutes($lastActivityAt) >= 15
            : (($scanRequested || $site->crawl_status === 'processing')
                && $queued > 0
            && $processing === 0
            && $lastActivityAt !== null
            && now()->diffInMinutes($lastActivityAt) >= 15);

        $isLimited = $settings->max_pages > 0 && $discovered >= $settings->max_pages;

        $hasLiveScanSignal = $activeScan !== null
            || $scanRequested
            || ($site->crawl_status === 'processing' && ($queued > 0 || $processing > 0));

        $state = match (true) {
            $hasLiveScanSignal && $isStalled => 'stalled',
            $hasLiveScanSignal => 'scanning',
            $metricsScan === null => 'idle',
            $isLimited => 'limited',
            default => 'complete',
        };

        return new self(
            site: $site,
            metricsScan: $metricsScan,
            activeScan: $activeScan,
            state: $state,
            discovered: $discovered,
            crawled: $crawled,
            failed: $failed,
            limit: (int) ($settings->max_pages ?? 0),
            queued: $queued,
            processing: $processing,
            isStalled: $isStalled,
            isLimited: $isLimited,
            lastCompletedAt: $metricsScan?->completed_at,
            lastActivityAt: $lastActivityAt,
            opportunities: $opportunities,
        );
    }

    public function siteId(): ?int
    {
        return $this->site?->id;
    }

    public function scanRunId(): ?int
    {
        return $this->metricsScan?->id;
    }

    public function activeScanRunId(): ?int
    {
        return $this->activeScan?->id;
    }

    public function hasMetricsScan(): bool
    {
        return $this->metricsScan !== null;
    }
}