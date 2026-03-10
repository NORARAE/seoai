<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Site;
use App\Services\SiteCrawler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrawlSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:site {domain? : The domain to crawl (e.g., bionw.com)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl a site\'s homepage, discover links, and persist to database';

    /**
     * Execute the console command.
     */
    public function handle(SiteCrawler $crawler): int
    {
        $domain = $this->argument('domain');

        // If no domain provided, try to use the first site from database
        if (!$domain) {
            $site = Site::first();

            if ($site) {
                $domain = $site->domain;
                $this->info("No domain provided. Using first site from database: {$domain}");
            } else {
                $this->error('No domain provided and no sites found in database.');
                $this->info('Usage: php artisan crawl:site bionw.com');
                return self::FAILURE;
            }
        }

        $this->info("🔍 Starting crawl for: {$domain}");
        $this->newLine();

        try {
            // Find or create the Site record
            $site = Site::firstOrCreate(
                ['domain' => $domain],
                [
                    'name' => $domain,
                    'status' => 'active',
                    'crawl_status' => 'crawling',
                ]
            );

            // Update crawl status to "crawling"
            $site->update(['crawl_status' => 'crawling']);

            // Perform the crawl
            $result = $crawler->crawlHomepage($domain);

            // Handle crawl failure
            if (!$result['success']) {
                $site->update([
                    'crawl_status' => 'failed',
                    'last_crawled_at' => now(),
                ]);

                $this->error("❌ Crawl failed!");
                $this->error("URL: {$result['url']}");
                $this->error("Error: {$result['error']}");

                if ($result['status_code']) {
                    $this->warn("Status Code: {$result['status_code']}");
                }

                return self::FAILURE;
            }

            // Persist discovered pages to database
            $newPagesCount = 0;
            $existingPagesCount = 0;

            foreach ($result['links'] as $url) {
                // Extract path from URL
                $path = parse_url($url, PHP_URL_PATH) ?? '/';

                // Try to create page, or update if exists
                $page = Page::firstOrNew([
                    'site_id' => $site->id,
                    'url' => $url,
                ]);

                if (!$page->exists) {
                    $newPagesCount++;
                } else {
                    $existingPagesCount++;
                }

                $page->fill([
                    'path' => $path,
                    'crawl_status' => 'discovered',
                    'last_crawled_at' => now(),
                ]);

                $page->save();
            }

            // Update Site record with crawl summary
            $site->update([
                'pages_crawled' => $site->pages()->count(),
                'crawl_status' => 'completed',
                'last_crawled_at' => now(),
            ]);

            // Display summary
            $this->info("✅ Crawl completed successfully!");
            $this->newLine();

            $this->info("📋 Summary:");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Site Domain', $site->domain],
                    ['Site URL', $result['url']],
                    ['HTTP Status', $result['status_code']],
                    ['Links Discovered', count($result['links'])],
                    ['New Pages Created', $newPagesCount],
                    ['Existing Pages Found', $existingPagesCount],
                    ['Total Pages in DB', $site->pages_crawled],
                    ['Crawl Status', $site->crawl_status],
                ]
            );

            $this->newLine();
            $this->info('✨ Results saved to database!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            // Update site status on exception
            if (isset($site)) {
                $site->update([
                    'crawl_status' => 'failed',
                    'last_crawled_at' => now(),
                ]);
            }

            $this->error("❌ An error occurred: {$e->getMessage()}");
            $this->error("Stack trace: {$e->getTraceAsString()}");

            return self::FAILURE;
        }
    }
}
