<?php

namespace App\Console\Commands;

use App\Models\LocationPage;
use App\Models\State;
use App\Services\LocationPageRenderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportLocationPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:export-location-pages
                            {--state= : Filter by state code (e.g., WA)}
                            {--status= : Filter by publication status (draft|published|archived)}
                            {--quality= : Filter by content quality status (unreviewed|edited|approved|excluded)}
                            {--type= : Filter by page type (county_hub|service_city)}
                            {--include-html : Include rendered HTML in export}
                            {--format=json : Export format (only json supported currently)}
                            {--output=storage/app/exports/location-pages.json : Output file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export location pages to JSON format with optional filters';

    /**
     * Execute the console command.
     */
    public function handle(LocationPageRenderService $renderService): int
    {
        $this->info('Starting location pages export...');
        $this->newLine();

        // Build query with filters
        $query = LocationPage::query()
            ->with([
                'state',
                'county',
                'city',
                'service',
                'approvedBy:id,name',
            ]);

        // Apply filters
        if ($stateCode = $this->option('state')) {
            $state = State::where('code', strtoupper($stateCode))->first();
            if (!$state) {
                $this->error("State '{$stateCode}' not found.");
                return self::FAILURE;
            }
            $query->where('state_id', $state->id);
            $this->line("Filter: State = {$state->name} ({$state->code})");
        }

        if ($status = $this->option('status')) {
            $allowedStatuses = ['draft', 'published', 'archived'];
            if (!in_array($status, $allowedStatuses)) {
                $this->error("Invalid status. Allowed values: " . implode(', ', $allowedStatuses));
                return self::FAILURE;
            }
            $query->where('status', $status);
            $this->line("Filter: Status = {$status}");
        }

        if ($quality = $this->option('quality')) {
            $allowedQualities = ['unreviewed', 'edited', 'approved', 'excluded'];
            if (!in_array($quality, $allowedQualities)) {
                $this->error("Invalid quality status. Allowed values: " . implode(', ', $allowedQualities));
                return self::FAILURE;
            }
            $query->where('content_quality_status', $quality);
            $this->line("Filter: Quality Status = {$quality}");
        }

        if ($type = $this->option('type')) {
            $allowedTypes = ['county_hub', 'service_city'];
            if (!in_array($type, $allowedTypes)) {
                $this->error("Invalid page type. Allowed values: " . implode(', ', $allowedTypes));
                return self::FAILURE;
            }
            $query->where('type', $type);
            $this->line("Filter: Page Type = {$type}");
        }

        $this->newLine();

        // Get pages
        $pages = $query->orderBy('type')
            ->orderByDesc('score')
            ->get();

        if ($pages->isEmpty()) {
            $this->warn('No pages found matching the filters.');
            return self::SUCCESS;
        }

        $this->info("Found {$pages->count()} pages to export.");
        $this->newLine();

        $includeHtml = $this->option('include-html');
        if ($includeHtml) {
            $this->line('Option: Including rendered HTML');
            $this->newLine();
        }

        // Transform to export format
        $export = [
            'exported_at' => now()->toIso8601String(),
            'total_pages' => $pages->count(),
            'filters' => [
                'state' => $this->option('state'),
                'status' => $this->option('status'),
                'quality' => $this->option('quality'),
                'type' => $this->option('type'),
            ],
            'options' => [
                'include_html' => $includeHtml,
            ],
            'pages' => $pages->map(function ($page) use ($renderService, $includeHtml) {
                // Use cached HTML if available, otherwise render on demand
                $renderedHtml = null;
                if ($includeHtml) {
                    if ($page->rendered_html_cache && !$page->needs_render) {
                        $renderedHtml = $page->rendered_html_cache;
                    } else {
                        $renderedHtml = $renderService->render($page);
                    }
                }

                return [
                    'id' => $page->id,
                    'type' => $page->type,
                    'slug' => $page->slug,
                    'url_path' => $page->url_path,
                    'canonical_url' => $page->canonical_url,
                    
                    // SEO fields
                    'title' => $page->title,
                    'meta_title' => $page->meta_title,
                    'meta_description' => $page->meta_description,
                    'h1' => $page->h1,
                    
                    // Content
                    'body_sections' => $page->body_sections_json,
                    'internal_links' => $page->internal_links_json,
                    'rendered_html' => $renderedHtml,
                    'rendered_excerpt' => $page->rendered_excerpt_cache,
                    
                    // Schema.org structured data
                    'faq_schema' => $page->faq_schema_json,
                    'service_schema' => $page->service_schema_json,
                    'local_business_schema' => $page->local_business_schema_json,
                    
                    // Metadata
                    'score' => $page->score,
                    'status' => $page->status,
                    'is_indexable' => $page->is_indexable,
                    
                    // Review workflow
                    'needs_review' => $page->needs_review,
                    'review_notes' => $page->review_notes,
                    'content_quality_status' => $page->content_quality_status,
                    'approved_at' => $page->approved_at?->toIso8601String(),
                    'approved_by' => $page->approvedBy?->name,
                    
                    // Render cache metadata
                    'render_version' => $page->render_version,
                    'rendered_at' => $page->rendered_at?->toIso8601String(),
                    'needs_render' => $page->needs_render,
                    
                    // Relationships
                    'state' => [
                        'name' => $page->state->name,
                        'code' => $page->state->code,
                    ],
                    'county' => [
                        'name' => $page->county->name,
                        'is_priority' => $page->county->is_priority ?? false,
                    ],
                    'city' => $page->city ? [
                        'name' => $page->city->name,
                        'population' => $page->city->population,
                        'is_county_seat' => $page->city->is_county_seat ?? false,
                        'is_priority' => $page->city->is_priority ?? false,
                    ] : null,
                    'service' => $page->service ? [
                        'name' => $page->service->name,
                        'slug' => $page->service->slug,
                    ] : null,
                    
                    // Timestamps
                    'generated_at' => $page->generated_at?->toIso8601String(),
                    'created_at' => $page->created_at->toIso8601String(),
                    'updated_at' => $page->updated_at->toIso8601String(),
                ];
            })->values(),
        ];

        // Save to file
        $outputPath = $this->option('output');
        $format = $this->option('format');

        if ($format !== 'json') {
            $this->error("Only 'json' format is currently supported.");
            return self::FAILURE;
        }

        // Ensure directory exists
        $directory = dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Write file
        $json = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        file_put_contents($outputPath, $json);

        $this->newLine();
        $this->info("✓ Export complete!");
        $this->line("Output: {$outputPath}");
        $this->line("Size: " . number_format(strlen($json) / 1024, 2) . " KB");
        
        // Summary by type
        $byType = $pages->groupBy('type');
        $this->newLine();
        $this->line('Summary by Type:');
        foreach ($byType as $type => $typePages) {
            $this->line("  {$type}: {$typePages->count()}");
        }

        // Summary by quality status
        $byQuality = $pages->groupBy('content_quality_status');
        $this->newLine();
        $this->line('Summary by Quality Status:');
        foreach ($byQuality as $quality => $qualityPages) {
            $this->line("  {$quality}: {$qualityPages->count()}");
        }

        return self::SUCCESS;
    }
}
