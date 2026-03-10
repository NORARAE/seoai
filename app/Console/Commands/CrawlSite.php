<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\SiteCrawler;
use Illuminate\Console\Command;

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
    protected $description = 'Crawl a site\'s homepage and display all discovered links';

    /**
     * Execute the console command.
     */
    public function handle(SiteCrawler $crawler): int
    {
        $domain = $this->argument('domain');

        // If no domain provided, try to use the first site from database or use a default
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

        $this->info("Starting crawl for: {$domain}");
        $this->newLine();

        // Perform the crawl
        $result = $crawler->crawlHomepage($domain);

        // Display results
        if (!$result['success']) {
            $this->error("❌ Crawl failed!");
            $this->error("URL: {$result['url']}");
            $this->error("Error: {$result['error']}");

            if ($result['status_code']) {
                $this->warn("Status Code: {$result['status_code']}");
            }

            return self::FAILURE;
        }

        $this->info("✅ Crawl successful!");
        $this->info("URL: {$result['url']}");
        $this->info("Status Code: {$result['status_code']}");
        $this->newLine();

        $linkCount = count($result['links']);
        $this->info("📊 Found {$linkCount} internal link(s):");
        $this->newLine();

        if (empty($result['links'])) {
            $this->warn('No internal links found on this page.');
        } else {
            // Display links in a table
            $tableData = array_map(fn($link, $index) => [
                'no' => $index + 1,
                'url' => $link,
            ], $result['links'], array_keys($result['links']));

            $this->table(
                ['#', 'URL'],
                $tableData
            );
        }

        $this->newLine();
        $this->info('✨ Crawl complete!');

        return self::SUCCESS;
    }
}
