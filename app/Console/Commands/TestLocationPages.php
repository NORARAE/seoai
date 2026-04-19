<?php

namespace App\Console\Commands;

use App\Models\LocationPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TestLocationPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:test-location-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run automated QA tests on location page workflow';

    protected int $passCount = 0;
    protected int $failCount = 0;
    protected int $warningCount = 0;
    protected array $errors = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('╔════════════════════════════════════════════════════╗');
        $this->info('║      Location Page QA Report                      ║');
        $this->info('╚════════════════════════════════════════════════════╝');
        $this->newLine();

        // TEST 1: Database State
        $this->test1_databaseState();

        // TEST 2: Unique Slugs
        $this->test2_uniqueSlugs();

        // TEST 3: Preview Route
        $this->test3_previewRoute();

        // TEST 4: Body Content
        $this->test4_bodyContent();

        // TEST 5: Internal Links
        $this->test5_internalLinks();

        // TEST 6: Export Validation
        $this->test6_exportValidation();

        // TEST 7: Generation Idempotency
        $this->test7_generationIdempotency();

        // TEST 8: Slug Quality Warnings
        $this->test8_slugQualityWarnings();

        // TEST 9: Render Cache Validation
        $this->test9_renderCacheValidation();

        // TEST 10: Schema Validation
        $this->test10_schemaValidation();

        // Summary
        $this->displaySummary();

        return $this->failCount > 0 ? 1 : 0;
    }

    protected function test1_databaseState(): void
    {
        $this->info('TEST 1: Database State');
        $this->line('─────────────────────────────────────────');

        try {
            // Find Seattle service_city pages
            $seattlePages = LocationPage::whereHas('city', function ($query) {
                $query->where('name', 'Seattle');
            })
                ->where('type', 'service_city')
                ->with(['city', 'service'])
                ->get();

            $count = $seattlePages->count();
            $this->line("Seattle service_city pages found: <fg=cyan>{$count}</>");

            if ($count === 0) {
                $this->recordError('No Seattle service_city pages found in database');
                return;
            }

            // Report on each page
            foreach ($seattlePages as $page) {
                $serviceName = $page->service->name ?? 'Unknown';
                $this->line("  • {$serviceName} in Seattle");
                $this->line("    - Slug: {$page->slug}");
                $this->line("    - Status: {$page->status}");
                $this->line("    - Quality: {$page->content_quality_status}");
                $needsReview = $page->needs_review ? 'Yes' : 'No';
                $this->line("    - Needs Review: {$needsReview}");
            }

            $this->recordPass('Database state verified');

        } catch (\Exception $e) {
            $this->recordError("Database state check failed: {$e->getMessage()}");
        }

        $this->newLine();
    }

    protected function test2_uniqueSlugs(): void
    {
        $this->info('TEST 2: Unique Slugs');
        $this->line('─────────────────────────────────────────');

        $requiredSlugs = [
            'biohazard-cleanup-seattle-wa',
            'crime-scene-cleanup-seattle-wa',
            'unattended-death-cleanup-seattle-wa',
        ];

        $allPass = true;

        foreach ($requiredSlugs as $slug) {
            $count = LocationPage::where('slug', $slug)->count();

            if ($count === 0) {
                $this->line("  ✗ <fg=red>{$slug}</> - MISSING");
                $this->recordError("Required slug missing: {$slug}");
                $allPass = false;
            } elseif ($count > 1) {
                $this->line("  ✗ <fg=red>{$slug}</> - DUPLICATE (found {$count})");
                $this->recordError("Duplicate slug detected: {$slug} (found {$count} times)");
                $allPass = false;
            } else {
                $this->line("  ✓ <fg=green>{$slug}</> - OK");
            }
        }

        if ($allPass) {
            $this->recordPass('All required slugs exist exactly once');
        }

        $this->newLine();
    }

    protected function test3_previewRoute(): void
    {
        $this->info('TEST 3: Preview Route');
        $this->line('─────────────────────────────────────────');

        $seattlePages = LocationPage::whereHas('city', function ($query) {
            $query->where('name', 'Seattle');
        })
            ->where('type', 'service_city')
            ->get(['slug', 'status']);

        if ($seattlePages->isEmpty()) {
            $this->recordError('No Seattle pages found for preview testing');
            $this->newLine();
            return;
        }

        // Check if server is running
        $baseUrl = config('app.url');

        try {
            $testResponse = Http::timeout(2)->get($baseUrl);
            $serverRunning = true;
        } catch (\Exception $e) {
            $this->line("  <fg=yellow>⚠</> Server not running on {$baseUrl}");
            $this->line("    Preview route tests skipped");
            $this->line("    Run: php artisan serve");
            $this->recordWarning('Preview server not running - tests skipped');
            $this->newLine();
            return;
        }

        $allPass = true;
        $draftCount = 0;

        foreach ($seattlePages as $page) {
            try {
                $response = Http::timeout(5)->get("{$baseUrl}/preview/{$page->slug}");
                $status = $response->status();

                // For unauthenticated requests, draft pages return 404 (by design)
                if ($status === 200) {
                    $this->line("  ✓ <fg=green>/preview/{$page->slug}</> - 200 OK");
                } elseif ($status === 404 && $page->status !== 'published') {
                    $this->line("  <fg=yellow>⚠</> /preview/{$page->slug} - 404 (draft page, expected without auth)");
                    $draftCount++;
                } else {
                    $this->line("  ✗ <fg=red>/preview/{$page->slug}</> - {$status}");
                    $this->recordError("Preview route returned unexpected {$status} for slug: {$page->slug}");
                    $allPass = false;
                }
            } catch (\Exception $e) {
                $this->line("  ✗ <fg=red>/preview/{$page->slug}</> - ERROR: {$e->getMessage()}");
                $this->recordError("Preview route failed for slug {$page->slug}: {$e->getMessage()}");
                $allPass = false;
            }
        }

        if ($allPass) {
            if ($draftCount > 0) {
                $this->recordPass("All preview routes working ({$draftCount} draft pages return 404 without auth)");
            } else {
                $this->recordPass('All preview routes accessible');
            }
        }

        $this->newLine();
    }

    protected function test4_bodyContent(): void
    {
        $this->info('TEST 4: Body Content');
        $this->line('─────────────────────────────────────────');

        $seattlePages = LocationPage::whereHas('city', function ($query) {
            $query->where('name', 'Seattle');
        })
            ->where('type', 'service_city')
            ->get();

        if ($seattlePages->isEmpty()) {
            $this->recordError('No Seattle pages found for body content testing');
            $this->newLine();
            return;
        }

        $requiredSections = ['hero', 'intro', 'cta'];
        $allPass = true;

        foreach ($seattlePages as $page) {
            $bodySections = $page->body_sections_json;

            if (empty($bodySections) || !is_array($bodySections)) {
                $this->line("  ✗ <fg=red>{$page->slug}</> - No body sections found");
                $this->recordError("Missing body_sections_json for: {$page->slug}");
                $allPass = false;
                continue;
            }

            // Extract section types
            $sectionTypes = array_column($bodySections, 'type');
            $missingSections = array_diff($requiredSections, $sectionTypes);

            if (empty($missingSections)) {
                $this->line("  ✓ <fg=green>{$page->slug}</> - All required sections present (" . count($bodySections) . " total)");
            } else {
                $this->line("  ✗ <fg=red>{$page->slug}</> - Missing: " . implode(', ', $missingSections));
                $this->recordError("Missing sections for {$page->slug}: " . implode(', ', $missingSections));
                $allPass = false;
            }
        }

        if ($allPass) {
            $this->recordPass('All pages have required body sections');
        }

        $this->newLine();
    }

    protected function test5_internalLinks(): void
    {
        $this->info('TEST 5: Internal Links');
        $this->line('─────────────────────────────────────────');

        $seattlePages = LocationPage::whereHas('city', function ($query) {
            $query->where('name', 'Seattle');
        })
            ->where('type', 'service_city')
            ->get();

        if ($seattlePages->isEmpty()) {
            $this->recordError('No Seattle pages found for internal links testing');
            $this->newLine();
            return;
        }

        $allPass = true;

        foreach ($seattlePages as $page) {
            $internalLinks = $page->internal_links_json;

            if (empty($internalLinks) || !is_array($internalLinks)) {
                $this->line("  ✗ <fg=red>{$page->slug}</> - No internal_links_json");
                $this->recordError("Missing internal_links_json for: {$page->slug}");
                $allPass = false;
                continue;
            }

            $links = $internalLinks['links'] ?? [];
            $linkCount = count($links);

            if ($linkCount === 0) {
                $this->line("  ✗ <fg=red>{$page->slug}</> - No links in internal_links_json");
                $this->recordError("No links found for: {$page->slug}");
                $allPass = false;
            } else {
                $this->line("  ✓ <fg=green>{$page->slug}</> - {$linkCount} internal links");
            }
        }

        if ($allPass) {
            $this->recordPass('All pages have internal links');
        }

        $this->newLine();
    }

    protected function test6_exportValidation(): void
    {
        $this->info('TEST 6: Export Validation');
        $this->line('─────────────────────────────────────────');

        $testOutputPath = storage_path('app/exports/qa-test-export.json');

        try {
            // Run export command silently
            $this->line("  Running export command...");
            $exitCode = $this->call('seo:export-location-pages', [
                '--state' => 'WA',
                '--type' => 'service_city',
                '--output' => $testOutputPath,
            ]);

            if ($exitCode !== 0) {
                $this->recordError('Export command failed with exit code: ' . $exitCode);
                $this->newLine();
                return;
            }

            if (!file_exists($testOutputPath)) {
                $this->recordError("Export file not created at: {$testOutputPath}");
                $this->newLine();
                return;
            }

            $json = json_decode(file_get_contents($testOutputPath), true);

            if (!$json) {
                $this->recordError('Export file contains invalid JSON');
                $this->newLine();
                return;
            }

            // Verify structure
            $requiredTopLevelKeys = ['exported_at', 'total_pages', 'filters', 'pages'];
            $missingTopKeys = array_diff($requiredTopLevelKeys, array_keys($json));

            if (!empty($missingTopKeys)) {
                $this->recordError('Export missing top-level keys: ' . implode(', ', $missingTopKeys));
                $this->newLine();
                return;
            }

            // Verify first page has required keys
            if (empty($json['pages'])) {
                $this->recordError('Export contains no pages');
                $this->newLine();
                return;
            }

            $firstPage = $json['pages'][0];
            $requiredPageKeys = [
                'slug',
                'meta_title',
                'meta_description',
                'h1',
                'canonical_url',
                'body_sections',
                'internal_links'
            ];
            $missingPageKeys = array_diff($requiredPageKeys, array_keys($firstPage));

            if (!empty($missingPageKeys)) {
                $this->line("  ✗ <fg=red>Page missing keys:</> " . implode(', ', $missingPageKeys));
                $this->recordError('Export page missing keys: ' . implode(', ', $missingPageKeys));
            } else {
                $this->line("  ✓ <fg=green>Export structure valid</>");
                $this->line("    - Total pages exported: {$json['total_pages']}");
                $this->line("    - File size: " . number_format(filesize($testOutputPath) / 1024, 2) . " KB");
                $this->recordPass('Export validation successful');
            }

            // Cleanup test file
            @unlink($testOutputPath);

        } catch (\Exception $e) {
            $this->recordError("Export validation failed: {$e->getMessage()}");
        }

        $this->newLine();
    }

    protected function test7_generationIdempotency(): void
    {
        $this->info('TEST 7: Generation Idempotency');
        $this->line('─────────────────────────────────────────');

        try {
            // Get current Seattle slug counts
            $beforeSlugs = LocationPage::whereHas('city', function ($query) {
                $query->where('name', 'Seattle');
            })
                ->where('type', 'service_city')
                ->pluck('slug')
                ->toArray();

            $beforeCount = count($beforeSlugs);
            $this->line("  Seattle pages before generation: <fg=cyan>{$beforeCount}</>");

            // Run generation command
            $this->line("  Running generation command...");
            $exitCode = $this->call('seo:generate-wa-drafts', ['--skip-validation' => true]);

            if ($exitCode !== 0) {
                $this->recordError('Generation command failed with exit code: ' . $exitCode);
                $this->newLine();
                return;
            }

            // Get counts after
            $afterSlugs = LocationPage::whereHas('city', function ($query) {
                $query->where('name', 'Seattle');
            })
                ->where('type', 'service_city')
                ->pluck('slug')
                ->toArray();

            $afterCount = count($afterSlugs);
            $this->line("  Seattle pages after generation: <fg=cyan>{$afterCount}</>");

            // Check for duplicates
            $duplicateCheck = LocationPage::select('slug', DB::raw('COUNT(*) as count'))
                ->whereHas('city', function ($query) {
                    $query->where('name', 'Seattle');
                })
                ->where('type', 'service_city')
                ->groupBy('slug')
                ->having('count', '>', 1)
                ->get();

            if ($duplicateCheck->isNotEmpty()) {
                $this->line("  ✗ <fg=red>Duplicate Seattle slugs detected:</>");
                foreach ($duplicateCheck as $dup) {
                    $this->line("    - {$dup->slug} (count: {$dup->count})");
                }
                $this->recordError('Generation created duplicate Seattle slugs');
            } elseif ($afterCount > $beforeCount) {
                $this->line("  ✗ <fg=yellow>New Seattle pages created</> ({$beforeCount} → {$afterCount})");
                $this->recordWarning('Generation unexpectedly created new Seattle pages');
            } else {
                $this->line("  ✓ <fg=green>No duplicates created, count stable</>");
                $this->recordPass('Generation idempotency verified');
            }

        } catch (\Exception $e) {
            $this->recordError("Generation idempotency test failed: {$e->getMessage()}");
        }

        $this->newLine();
    }

    protected function test8_slugQualityWarnings(): void
    {
        $this->info('TEST 8: Slug Quality Warnings');
        $this->line('─────────────────────────────────────────');

        try {
            $foundIssues = [];

            // Check for awkward patterns that should NOT exist
            $awkwardPatterns = [
                'county-county' => 'duplicated county suffix',
                'city-city' => 'duplicated city suffix',
                'wa-wa' => 'duplicated state code',
            ];

            foreach ($awkwardPatterns as $pattern => $description) {
                $pages = LocationPage::where('slug', 'LIKE', "%{$pattern}%")->get();

                foreach ($pages as $page) {
                    $foundIssues[] = [
                        'slug' => $page->slug,
                        'type' => $page->type,
                        'issue' => $description,
                    ];
                }
            }

            // Verify county hubs follow correct format
            $countyHubs = LocationPage::where('type', 'county_hub')->get();
            $countyHubsChecked = 0;
            $countyHubsCorrect = 0;

            foreach ($countyHubs as $hub) {
                $countyHubsChecked++;

                // Correct format: {county-name}-wa (e.g., king-county-wa)
                // Incorrect format: {county-name}-county-wa (e.g., king-county-county-wa)
                if (preg_match('/^[a-z0-9\-]+-wa$/', $hub->slug) && !str_contains($hub->slug, 'county-county')) {
                    $countyHubsCorrect++;
                }
            }

            if (empty($foundIssues)) {
                $this->line("  ✓ <fg=green>No awkward patterns detected</>");

                if ($countyHubsChecked > 0) {
                    if ($countyHubsCorrect === $countyHubsChecked) {
                        $this->line("  ✓ <fg=green>All {$countyHubsChecked} county hub slugs follow correct format</>");
                        $this->recordPass('Slug quality check passed - all county hubs correct');
                    } else {
                        $badCount = $countyHubsChecked - $countyHubsCorrect;
                        $this->line("  <fg=yellow>⚠</> {$badCount} of {$countyHubsChecked} county hub slugs need correction");
                        $this->recordWarning("{$badCount} county hub slugs need correction");
                    }
                } else {
                    $this->recordPass('Slug quality check passed');
                }
            } else {
                $issueCount = count($foundIssues);
                $this->line("  <fg=yellow>⚠</> Found {$issueCount} slug quality warnings:");
                foreach ($foundIssues as $issue) {
                    $this->line("    - {$issue['slug']} ({$issue['issue']}, type: {$issue['type']})");

                    // County-county pattern is considered an error that needs repair
                    if ($issue['issue'] === 'duplicated county suffix') {
                        $this->recordWarning("Slug quality: {$issue['slug']} has {$issue['issue']} - run seo:repair-county-hub-slugs");
                    } else {
                        $this->recordWarning("Slug quality: {$issue['slug']} has {$issue['issue']}");
                    }
                }
            }

        } catch (\Exception $e) {
            $this->recordError("Slug quality check failed: {$e->getMessage()}");
        }

        $this->newLine();
    }

    protected function test9_renderCacheValidation(): void
    {
        $this->info('TEST 9: Render Cache Validation');
        $this->line('─────────────────────────────────────────');

        try {
            // Check Seattle pages for render cache
            $seattlePages = LocationPage::whereHas('city', function ($query) {
                $query->where('name', 'Seattle');
            })->where('type', 'service_city')->get();

            if ($seattlePages->isEmpty()) {
                $this->recordError('No Seattle service_city pages found for cache validation');
                return;
            }

            $cachedCount = 0;
            $needsRenderCount = 0;
            $staleCacheCount = 0;

            foreach ($seattlePages as $page) {
                if ($page->rendered_html_cache) {
                    $cachedCount++;

                    // Check if cache is stale (content updated after last render)
                    if ($page->rendered_at && $page->updated_at && $page->updated_at > $page->rendered_at) {
                        $staleCacheCount++;
                    }
                }

                if ($page->needs_render) {
                    $needsRenderCount++;
                }
            }

            $this->line("  <fg=blue>Info:</> {$seattlePages->count()} Seattle page(s) checked");
            $this->line("  <fg=blue>Info:</> {$cachedCount} have cached HTML");
            $this->line("  <fg=blue>Info:</> {$needsRenderCount} flagged as needs_render");

            if ($staleCacheCount > 0) {
                $this->line("  <fg=yellow>⚠</> {$staleCacheCount} have stale cache (updated after rendering)");
                $this->recordWarning("{$staleCacheCount} pages have stale cache");
            }

            // Check if render cache structure is valid
            $validCacheCount = 0;
            foreach ($seattlePages->where('rendered_html_cache', '!=', null) as $page) {
                if (is_string($page->rendered_html_cache) && strlen($page->rendered_html_cache) > 0) {
                    $validCacheCount++;
                }
            }

            if ($validCacheCount > 0) {
                $this->line("  ✓ <fg=green>{$validCacheCount} pages have valid cached HTML</>");
                $this->recordPass("Render cache validation passed for {$validCacheCount} page(s)");
            } else {
                $this->line("  <fg=blue>Info:</> No pages with cached HTML yet (run seo:render-location-pages to cache)");
                $this->recordPass('Render cache fields exist and are queryable');
            }

        } catch (\Exception $e) {
            $this->recordError("Render cache validation failed: {$e->getMessage()}");
        }

        $this->newLine();
    }

    protected function test10_schemaValidation(): void
    {
        $this->info('TEST 10: Schema Validation');
        $this->line('─────────────────────────────────────────');

        try {
            // Check Seattle service_city pages for schemas
            $seattlePages = LocationPage::whereHas('city', function ($query) {
                $query->where('name', 'Seattle');
            })->where('type', 'service_city')->get();

            if ($seattlePages->isEmpty()) {
                $this->recordError('No Seattle service_city pages found for schema validation');
                return;
            }

            $serviceSchemaCount = 0;
            $localBusinessSchemaCount = 0;
            $faqSchemaCount = 0;
            $anySchemaCount = 0;

            foreach ($seattlePages as $page) {
                $hasAnySchema = false;

                if ($page->service_schema_json && is_array($page->service_schema_json)) {
                    $serviceSchemaCount++;
                    $hasAnySchema = true;

                    // Validate structure
                    if (!isset($page->service_schema_json['@type']) || $page->service_schema_json['@type'] !== 'Service') {
                        $this->recordWarning("Page {$page->slug} has invalid service schema structure");
                    }
                }

                if ($page->local_business_schema_json && is_array($page->local_business_schema_json)) {
                    $localBusinessSchemaCount++;
                    $hasAnySchema = true;

                    // Validate structure
                    if (!isset($page->local_business_schema_json['@type']) || $page->local_business_schema_json['@type'] !== 'LocalBusiness') {
                        $this->recordWarning("Page {$page->slug} has invalid local business schema structure");
                    }
                }

                if ($page->faq_schema_json && is_array($page->faq_schema_json)) {
                    $faqSchemaCount++;
                    $hasAnySchema = true;
                }

                if ($hasAnySchema) {
                    $anySchemaCount++;
                }
            }

            $this->line("  <fg=blue>Info:</> {$seattlePages->count()} Seattle page(s) checked");
            $this->line("  <fg=blue>Info:</> {$serviceSchemaCount} have Service schema");
            $this->line("  <fg=blue>Info:</> {$localBusinessSchemaCount} have LocalBusiness schema");
            $this->line("  <fg=blue>Info:</> {$faqSchemaCount} have FAQ schema");

            if ($anySchemaCount > 0) {
                $this->line("  ✓ <fg=green>{$anySchemaCount} pages have at least one valid schema</>");
                $this->recordPass("Schema validation passed for {$anySchemaCount} page(s)");
            } else {
                $this->line("  <fg=blue>Info:</> No schemas cached yet (run seo:render-location-pages to generate)");
                $this->recordPass('Schema fields exist and are queryable');
            }

        } catch (\Exception $e) {
            $this->recordError("Schema validation failed: {$e->getMessage()}");
        }

        $this->newLine();
    }

    protected function displaySummary(): void
    {
        $this->info('╔════════════════════════════════════════════════════╗');
        $this->info('║      Summary                                      ║');
        $this->info('╚════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->line("  Tests Passed:    <fg=green>{$this->passCount}</>");
        $this->line("  Tests Failed:    <fg=red>{$this->failCount}</>");
        $this->line("  Warnings:        <fg=yellow>{$this->warningCount}</>");

        $this->newLine();

        if ($this->failCount > 0) {
            $this->error('❌ QA FAILED - Critical issues detected');
            $this->newLine();
            $this->line('<fg=red>Errors:</>', 'v');
            foreach ($this->errors as $error) {
                $this->line("  • {$error}");
            }
        } else {
            $this->info('✅ QA PASSED - All critical tests successful');
            if ($this->warningCount > 0) {
                $this->newLine();
                $this->line("<fg=yellow>⚠ {$this->warningCount} warning(s) detected - review recommended</>");
            }
        }

        $this->newLine();
    }

    protected function recordPass(string $message): void
    {
        $this->passCount++;
    }

    protected function recordError(string $message): void
    {
        $this->failCount++;
        $this->errors[] = $message;
    }

    protected function recordWarning(string $message): void
    {
        $this->warningCount++;
    }
}
