<?php

namespace App\Console\Commands;

use App\Models\InternalLink;
use App\Models\Page;
use App\Models\Site;
use App\Services\SiteCrawler;
use Illuminate\Console\Command;

class CrawlSite extends Command
{
    protected $signature = 'crawl:site {domain? : The domain to crawl (e.g., bionw.com)}';
    
    protected $description = 'Crawl a site\'s homepage and persist discovered links to database';

    public function handle(SiteCrawler $crawler): int
    {
        $domain = $this->argument('domain');

        if (!$domain) {
            $site = Site::first();
            
            if (!$site) {
                $this->error('No domain provided and no sites found in database.');
                $this->info('Usage: php artisan crawl:site example.com');
                return self::FAILURE;
            }
            
            $domain = $site->domain;
            $this->info("Using first site from database: {$domain}");
        }

        $this->info("Crawling: {$domain}");

        try {
            // Find or create Site
            $site = Site::firstOrCreate(
                ['domain' => $domain],
                ['name' => $domain, 'status' => 'active']
            );

            // Perform crawl
            $result = $crawler->crawlHomepage($domain);

            if (!$result['success']) {
                $site->update([
                    'crawl_status' => 'failed',
                    'last_crawled_at' => now(),
                ]);

                $this->error("Crawl failed: {$result['error']}");
                return self::FAILURE;
            }

            // Save homepage page with metadata
            $homepageUrl = $result['url'];
            $homepage = Page::updateOrCreate(
                ['site_id' => $site->id, 'url' => $homepageUrl],
                [
                    'path' => parse_url($homepageUrl, PHP_URL_PATH) ?? '/',
                    'title' => $result['title'],
                    'status_code' => $result['status_code'],
                    'crawl_status' => 'completed',
                    'last_crawled_at' => now(),
                ]
            );

            $internalLinksCount = 0;

            // Save links and internal links
            foreach ($result['links'] as $linkData) {
                $targetUrl = $linkData['url'];
                $anchorText = $linkData['anchor_text'];

                // Skip if it's the homepage (already saved above)
                if ($targetUrl !== $homepageUrl) {
                    // Save target page (without metadata for now)
                    Page::updateOrCreate(
                        ['site_id' => $site->id, 'url' => $targetUrl],
                        [
                            'path' => parse_url($targetUrl, PHP_URL_PATH) ?? '/',
                            'last_crawled_at' => now(),
                        ]
                    );
                }

                // Save internal link
                InternalLink::updateOrCreate(
                    [
                        'site_id' => $site->id,
                        'source_url' => $homepageUrl,
                        'target_url' => $targetUrl,
                        'anchor_text' => $anchorText,
                    ],
                    [
                        'source_page_id' => $homepage->id,
                    ]
                );

                $internalLinksCount++;
            }

            // Update Site stats
            $site->update([
                'pages_crawled' => $site->pages()->count(),
                'crawl_status' => 'completed',
                'last_crawled_at' => now(),
            ]);

            // Sync page metrics
            $this->call('pages:sync-metrics', ['--site' => $domain]);

            // Display summary
            $this->info("✓ Success!");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Domain', $site->domain],
                    ['Homepage Title', $result['title'] ?? '—'],
                    ['HTTP Status', $result['status_code']],
                    ['Links Found', count($result['links'])],
                    ['Internal Links Saved', $internalLinksCount],
                    ['Total Pages', $site->pages_crawled],
                    ['Status', $site->crawl_status],
                ]
            );

            return self::SUCCESS;
            
        } catch (\Exception $e) {
            if (isset($site)) {
                $site->update(['crawl_status' => 'failed', 'last_crawled_at' => now()]);
            }

            $this->error("Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
