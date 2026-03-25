<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\SearchOpportunity\OpportunityMappingEngine;
use Illuminate\Console\Command;

class MapSearchOpportunitiesCommand extends Command
{
    protected $signature = 'search-opportunities:map {--site_id=}';

    protected $description = 'Build or refresh rule-based search opportunity mapping records';

    public function handle(OpportunityMappingEngine $engine): int
    {
        $siteId = $this->option('site_id');

        /** @var \Illuminate\Database\Eloquent\Collection<int, Site> $sites */
        $sites = $siteId
            ? Site::query()->whereKey($siteId)->get()
            : Site::query()->where('status', 'active')->get();

        if ($sites->isEmpty()) {
            $this->warn('No sites found to analyze.');

            return self::SUCCESS;
        }

        foreach ($sites as $site) {
            /** @var Site $site */
            $result = $engine->mapSite($site);

            $this->info(sprintf(
                'Site %d: created=%d updated=%d skipped=%d',
                $site->id,
                $result['created'],
                $result['updated'],
                $result['skipped'],
            ));

            foreach ($result['by_category'] as $category => $count) {
                $this->line(sprintf('  - %s: %d', $category, $count));
            }
        }

        return self::SUCCESS;
    }
}
