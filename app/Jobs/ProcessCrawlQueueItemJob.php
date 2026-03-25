<?php

namespace App\Jobs;

use App\Models\CrawlQueue;
use App\Services\Discovery\CrawlQueueService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessCrawlQueueItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [30, 300];

    public function __construct(public int $crawlQueueId) {}

    public function handle(CrawlQueueService $crawlQueueService): void
    {
        $item = CrawlQueue::with(['site', 'urlInventory'])->find($this->crawlQueueId);

        if (! $item) {
            return;
        }

        if (in_array($item->status, ['completed', 'failed'], true)) {
            Log::info('Crawl queue job skipped because item already finished', [
                'queue_id' => $item->id,
                'site_id' => $item->site_id,
                'status' => $item->status,
            ]);

            return;
        }

        $result = $crawlQueueService->processQueueItem($item);

        Log::info('Crawl queue item processed', [
            'queue_id' => $item->id,
            'site_id' => $item->site_id,
            'url' => $item->url,
            'result' => $result,
        ]);

        $crawlQueueService->refreshSiteCrawlStatus($item->site_id);

        if (! $item->site_id) {
            return;
        }

        $nextDelay = $crawlQueueService->nextDispatchDelay($item->site_id);

        if ($nextDelay === null) {
            return;
        }

        $throttleKey = sprintf('crawl-dispatch-throttle:%d', $item->site_id);

        if (Cache::has($throttleKey)) {
            return;
        }

        Cache::put($throttleKey, now()->toDateTimeString(), now()->addSeconds(10));

        DispatchCrawlQueueJob::dispatch($item->site_id, 50)
            ->delay(now()->addSeconds(max(2, min($nextDelay, 30))))
            ->onQueue('crawl');

        Log::info('Crawl dispatch continuation queued', [
            'site_id' => $item->site_id,
            'triggered_by_queue_id' => $item->id,
            'remaining_ready' => CrawlQueue::query()
                ->where('site_id', $item->site_id)
                ->where('status', 'queued')
                ->where(function ($query): void {
                    $query->whereNull('available_at')
                        ->orWhere('available_at', '<=', now());
                })
                ->count(),
            'next_dispatch_delay' => $nextDelay,
        ]);
    }
}
