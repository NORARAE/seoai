<?php

namespace App\Console\Commands;

use App\Models\Opportunity;
use App\Models\Page;
use App\Models\Site;
use Illuminate\Console\Command;

class GenerateOpportunities extends Command
{
    protected $signature = 'opportunities:generate {--site= : Generate for specific site domain only}';

    protected $description = 'Generate priority opportunities from page data';

    private const SCORES = [
        'broken_page' => 50,
        'orphan_page' => 40,
        'missing_title' => 30,
        'weak_internal_links' => 20,
        'awaiting_crawl' => 10,
    ];

    private const RECOMMENDATIONS = [
        'broken_page' => 'Fix broken page (4xx/5xx status code) to improve user experience and crawlability.',
        'orphan_page' => 'Add internal links to this page to improve discoverability and SEO value.',
        'missing_title' => 'Add a descriptive title tag to improve search visibility and click-through rate.',
        'weak_internal_links' => 'Increase internal links to this page to boost its authority and ranking potential.',
        'awaiting_crawl' => 'Crawl this page to extract metadata and analyze SEO opportunities.',
    ];

    public function handle(): int
    {
        $siteDomain = $this->option('site');
        
        if ($siteDomain) {
            $site = Site::where('domain', $siteDomain)->first();
            
            if (!$site) {
                $this->error("Site not found: {$siteDomain}");
                return self::FAILURE;
            }
            
            $sites = collect([$site]);
        } else {
            $sites = Site::all();
        }

        if ($sites->isEmpty()) {
            $this->warn('No sites found.');
            return self::SUCCESS;
        }

        $totalCreated = 0;

        foreach ($sites as $site) {
            // Clear existing open opportunities for this site
            Opportunity::where('site_id', $site->id)
                ->where('status', 'open')
                ->delete();

            $pages = Page::where('site_id', $site->id)->get();
            
            foreach ($pages as $page) {
                $opportunities = $this->detectIssues($page);
                
                foreach ($opportunities as $issue) {
                    Opportunity::create([
                        'site_id' => $site->id,
                        'page_id' => $page->id,
                        'issue_type' => $issue['type'],
                        'priority_score' => self::SCORES[$issue['type']],
                        'status' => 'open',
                        'recommendation' => self::RECOMMENDATIONS[$issue['type']],
                    ]);
                    
                    $totalCreated++;
                }
            }
        }

        $this->info("✓ Generated {$totalCreated} opportunities");
        
        return self::SUCCESS;
    }

    private function detectIssues(Page $page): array
    {
        $issues = [];

        // Broken page (highest priority)
        if ($page->status_code && $page->status_code >= 400) {
            $issues[] = ['type' => 'broken_page'];
        }

        // Orphan page (no incoming links)
        if ($page->incoming_links_count === 0) {
            $issues[] = ['type' => 'orphan_page'];
        }

        // Missing title
        if (empty($page->title)) {
            $issues[] = ['type' => 'missing_title'];
        }

        // Weak internal links (1 incoming link only)
        if ($page->incoming_links_count === 1) {
            $issues[] = ['type' => 'weak_internal_links'];
        }

        // Awaiting crawl
        if ($page->crawl_status === 'discovered') {
            $issues[] = ['type' => 'awaiting_crawl'];
        }

        return $issues;
    }
}
