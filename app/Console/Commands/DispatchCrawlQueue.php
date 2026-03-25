<?php

namespace App\Console\Commands;

use App\Jobs\DispatchCrawlQueueJob;
use Illuminate\Console\Command;

class DispatchCrawlQueue extends Command
{
    protected $signature = 'crawl:dispatch {--site_id= : Optional site ID} {--limit=50 : Max queued URLs to dispatch}';

    protected $description = 'Dispatch crawl queue items to queue workers';

    public function handle(): int
    {
        $siteId = $this->option('site_id');
        $limit = (int) $this->option('limit');

        DispatchCrawlQueueJob::dispatch($siteId ? (int) $siteId : null, $limit)->onQueue('crawl');

        $this->info("Dispatched crawl queue orchestration job (site_id={$siteId}, limit={$limit}).");

        return self::SUCCESS;
    }
}
