<?php

namespace App\Jobs;

use App\Models\CrawlQueue;
use App\Models\Site;
use App\Services\Discovery\CrawlQueueService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DispatchCrawlQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?int $siteId = null, public int $limit = 50) {}

    public function handle(CrawlQueueService $crawlQueueService): void
    {
        $lockKey = sprintf('crawl-dispatch-lock:%s', $this->siteId ?? 'all');
        $lock = Cache::lock($lockKey, 10);

        if (! $lock->get()) {
            Log::info('Crawl dispatch skipped because another dispatcher holds the lock', [
                'site_id' => $this->siteId,
                'limit' => $this->limit,
            ]);

            return;
        }

        try {
            $site = $this->siteId ? Site::find($this->siteId) : null;

            if ($site && $crawlQueueService->hasQueuedBacklog($site->id) && $site->crawl_status !== 'processing') {
                $site->forceFill(['crawl_status' => 'processing'])->save();
            }

            $items = $crawlQueueService->claimDispatchableItems($this->siteId, $this->limit);

            if ($items->isEmpty()) {
                if ($site) {
                    $crawlQueueService->refreshSiteCrawlStatus($site);
                }

                $nextDelay = $this->siteId ? $crawlQueueService->nextDispatchDelay($this->siteId) : null;

                if ($this->siteId && $nextDelay !== null) {
                    DispatchCrawlQueueJob::dispatch($this->siteId, $this->limit)
                        ->delay(now()->addSeconds(max(5, min($nextDelay, 60))))
                        ->onQueue('crawl');
                }

                Log::info('Crawl dispatch found no ready items', [
                    'site_id' => $this->siteId,
                    'limit' => $this->limit,
                    'queued_backlog' => $this->siteId ? CrawlQueue::query()->where('site_id', $this->siteId)->where('status', 'queued')->count() : null,
                    'next_dispatch_delay' => $nextDelay,
                ]);

                return;
            }

            foreach ($items as $item) {
                ProcessCrawlQueueItemJob::dispatch($item->id)->onQueue('crawl');
            }

            $firstSiteId = $this->siteId ?? $items->first()?->site_id;
            $remainingQueued = $firstSiteId
                ? CrawlQueue::query()->where('site_id', $firstSiteId)->where('status', 'queued')->count()
                : null;

            Log::info('Crawl dispatch released batch', [
                'site_id' => $this->siteId,
                'released_count' => $items->count(),
                'limit' => $this->limit,
                'remaining_queued' => $remainingQueued,
            ]);
        } finally {
            $lock->release();
        }
    }
}
