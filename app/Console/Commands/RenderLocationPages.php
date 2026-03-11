<?php

namespace App\Console\Commands;

use App\Models\LocationPage;
use App\Services\LocationPageRenderService;
use Illuminate\Console\Command;

class RenderLocationPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:render-location-pages
                            {--type= : Filter by page type (county_hub, service_city)}
                            {--quality= : Filter by content quality status (approved, edited, etc)}
                            {--state= : Filter by state code (e.g., WA)}
                            {--force : Force re-render even if already cached}
                            {--render-version=1.0 : Render version to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Render and cache HTML for location pages';

    protected LocationPageRenderService $renderService;

    public function __construct(LocationPageRenderService $renderService)
    {
        parent::__construct();
        $this->renderService = $renderService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting location page rendering...');
        $this->newLine();

        // Build query
        $query = LocationPage::query()->with(['state', 'county', 'city', 'service']);

        // Apply filters
        if ($type = $this->option('type')) {
            $query->where('type', $type);
            $this->info("Filter: Type = {$type}");
        }

        if ($quality = $this->option('quality')) {
            $query->where('content_quality_status', $quality);
            $this->info("Filter: Quality = {$quality}");
        }

        if ($state = $this->option('state')) {
            $query->whereHas('state', function ($q) use ($state) {
                $q->where('code', strtoupper($state));
            });
            $this->info("Filter: State = {$state}");
        }

        // Filter by needs_render unless forced
        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->where('needs_render', true)
                  ->orWhereNull('rendered_at');
            });
            $this->info("Filter: Only pages needing render");
        } else {
            $this->info("Option: Force re-render all matching pages");
        }

        $pages = $query->get();

        if ($pages->isEmpty()) {
            $this->warn('No pages found matching criteria.');
            return self::SUCCESS;
        }

        $this->info("Found {$pages->count()} pages to render.");
        $this->newLine();

        $version = $this->option('render-version');
        $bar = $this->output->createProgressBar($pages->count());
        $bar->start();

        $rendered = 0;
        $errors = 0;

        foreach ($pages as $page) {
            try {
                $this->renderService->renderAndCache($page, $version);
                $rendered++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nError rendering page {$page->slug}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("✓ Rendering complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Pages rendered', $rendered],
                ['Errors', $errors],
                ['Render version', $version],
            ]
        );

        return self::SUCCESS;
    }
}
