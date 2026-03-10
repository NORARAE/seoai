<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPageMetrics extends Command
{
    protected $signature = 'pages:sync-metrics {--site= : Sync metrics for a specific site domain}';

    protected $description = 'Synchronize page internal link metrics (incoming/outgoing counts)';

    public function handle(): int
    {
        $siteDomain = $this->option('site');
        
        if ($siteDomain) {
            $site = Site::where('domain', $siteDomain)->first();
            
            if (!$site) {
                $this->error("Site not found: {$siteDomain}");
                return self::FAILURE;
            }
            
            $pages = Page::where('site_id', $site->id)->get();
        } else {
            $pages = Page::all();
        }
        
        if ($pages->isEmpty()) {
            $this->warn('No pages found.');
            return self::SUCCESS;
        }
        
        $this->info("Syncing {$pages->count()} pages...");
        
        foreach ($pages as $page) {
            $page->update([
                'incoming_links_count' => DB::table('internal_links')
                    ->where('site_id', $page->site_id)
                    ->where('target_url', $page->url)
                    ->count(),
                    
                'outgoing_links_count' => DB::table('internal_links')
                    ->where('site_id', $page->site_id)
                    ->where('source_url', $page->url)
                    ->count(),
            ]);
        }
        
        $this->info("✓ Synced {$pages->count()} pages");
        
        return self::SUCCESS;
    }
}
