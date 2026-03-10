<?php

namespace App\Console\Commands;

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

            // Save discovered pages
            foreach ($result['links'] as $url) {
                Page::updateOrCreate(
                    ['site_id' => $site->id, 'url' => $url],
                    [
                        'path' => parse_url($url, PHP_URL_PATH) ?? '/',
                        'last_crawled_at' => now(),
                    ]
                );
            }

            // Update Site stats
            $site->update([
                'pages_crawled' => $site->pages()->count(),
                'crawl_status' => 'completed',
                'last_crawled_at' => now(),
            ]);

            // Display summary
            $this->info("✓ Success!");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Domain', $site->domain],
                    ['Links Found', count($result['links'])],
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
