<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\Discovery\DiscoveryOpportunityBridgeService;
use Illuminate\Console\Command;

class SyncDiscoveryOpportunities extends Command
{
    protected $signature = 'discovery:sync-opportunities {site? : Site ID or domain}';

    protected $description = 'Sync coverage flags, detect new opportunities, and dispatch approved payload jobs';

    public function handle(DiscoveryOpportunityBridgeService $bridge): int
    {
        $siteArg = $this->argument('site');

        $sites = Site::query();

        if ($siteArg) {
            $site = is_numeric($siteArg)
                ? Site::find($siteArg)
                : Site::where('domain', $siteArg)->first();

            if (! $site) {
                $this->error('Site not found.');

                return self::FAILURE;
            }

            $sites = collect([$site]);
        } else {
            $sites = $sites->get();
        }

        foreach ($sites as $site) {
            $result = $bridge->run($site);

            $detected = $result['detected'];
            $this->line(
                "{$site->domain}: " .
                "updated={$result['updated']}, covered={$result['covered']}, missing={$result['missing']}, " .
                "location_gaps={$detected['location_gaps']}, content_gaps={$detected['content_gaps']}, " .
                "link_opps={$detected['internal_link_opps']}, dispatched={$result['dispatched']}"
            );
        }

        return self::SUCCESS;
    }
}
