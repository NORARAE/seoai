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
        
        // Build query for pages to update
        $query = Page::query();
        
        if ($siteDomain) {
            $site = Site::where('domain', $siteDomain)->first();
            
            if (!$site) {
                $this->error("Site not found: {$siteDomain}");
                return self::FAILURE;
            }
            
            $query->where('site_id', $site->id);
            $this->info("Syncing metrics for site: {$siteDomain}");
        } else {
            $this->info("Syncing metrics for all pages...");
        }
        
        $pages = $query->get();
        $totalPages = $pages->count();
        
        if ($totalPages === 0) {
            $this->warn('No pages found to sync.');
            return self::SUCCESS;
        }
        
        $this->info("Processing {$totalPages} pages...");
        $bar = $this->output->createProgressBar($totalPages);
        $bar->start();
        
        $updated = 0;
        
        foreach ($pages as $page) {
            // Count incoming links (where this page is the target)
            $incomingCount = DB::table('internal_links')
                ->where('site_id', $page->site_id)
                ->where('target_url', $page->url)
                ->count();
            
            // Count outgoing links (where this page is the source)
            $outgoingCount = DB::table('internal_links')
                ->where('site_id', $page->site_id)
                ->where('source_url', $page->url)
                ->count();
            
            // Update page metrics
            $page->update([
                'incoming_links_count' => $incomingCount,
                'outgoing_links_count' => $outgoingCount,
            ]);
            
            $updated++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        // Display summary
        $this->info("✓ Successfully synced metrics for {$updated} pages");
        
        // Show aggregate stats
        $stats = Page::query()
            ->when($siteDomain, fn($q) => $q->where('site_id', $site->id))
            ->selectRaw('
                COUNT(*) as total_pages,
                SUM(incoming_links_count) as total_incoming,
                SUM(outgoing_links_count) as total_outgoing,
                AVG(incoming_links_count) as avg_incoming,
                AVG(outgoing_links_count) as avg_outgoing,
                MAX(incoming_links_count) as max_incoming,
                MAX(outgoing_links_count) as max_outgoing
            ')
            ->first();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Pages', number_format($stats->total_pages)],
                ['Total Incoming Links', number_format($stats->total_incoming)],
                ['Total Outgoing Links', number_format($stats->total_outgoing)],
                ['Avg Incoming per Page', number_format($stats->avg_incoming, 2)],
                ['Avg Outgoing per Page', number_format($stats->avg_outgoing, 2)],
                ['Max Incoming', number_format($stats->max_incoming)],
                ['Max Outgoing', number_format($stats->max_outgoing)],
            ]
        );
        
        return self::SUCCESS;
    }
}
