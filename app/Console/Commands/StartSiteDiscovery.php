<?php

namespace App\Console\Commands;

use App\Jobs\StartSiteDiscoveryJob;
use App\Models\Site;
use Illuminate\Console\Command;

class StartSiteDiscovery extends Command
{
    protected $signature = 'discovery:start {site? : Site ID or domain}';

    protected $description = 'Start discovery crawl initialization for a site (robots + sitemap + homepage queue)';

    public function handle(): int
    {
        $siteArg = $this->argument('site');

        $site = null;

        if ($siteArg) {
            $site = is_numeric($siteArg)
                ? Site::find($siteArg)
                : Site::where('domain', $siteArg)->first();
        }

        if (! $site) {
            $site = Site::first();
        }

        if (! $site) {
            $this->error('No site found. Provide a site ID or domain.');

            return self::FAILURE;
        }

        StartSiteDiscoveryJob::dispatch($site->id)->onQueue('crawl');

        $this->info("Discovery initialization queued for site {$site->domain} (ID: {$site->id}).");

        return self::SUCCESS;
    }
}
