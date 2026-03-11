<?php

namespace App\Console\Commands;

use App\Models\LocationPage;
use App\Services\SeoSlugGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairCountyHubSlugs extends Command
{
    protected $signature = 'seo:repair-county-hub-slugs
                            {--dry-run : Show what would be changed without making changes}
                            {--base-domain=https://example.com : Base domain for canonical URLs}';

    protected $description = 'Repair county hub slugs that have duplicated "county" suffix (e.g., king-county-county-wa → king-county-wa)';

    public function handle(SeoSlugGenerator $slugGenerator): int
    {
        $dryRun = $this->option('dry-run');
        $baseDomain = $this->option('base-domain');

        $this->info('County Hub Slug Repair');
        $this->line('═══════════════════════════════════════════');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Find all county hub pages
        $countyHubs = LocationPage::with(['state', 'county'])
            ->where('type', 'county_hub')
            ->get();

        if ($countyHubs->isEmpty()) {
            $this->warn('No county hub pages found.');
            return 0;
        }

        $this->info("Found {$countyHubs->count()} county hub page(s) to check");
        $this->newLine();

        $repaired = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($countyHubs as $page) {
            // Generate corrected slug using fixed logic
            $newUrlPath = $slugGenerator->generateCountyHubPath(
                $page->county->name,
                $page->state->code
            );
            $newSlug = $slugGenerator->generateSlugFromPath($newUrlPath);
            $newCanonicalUrl = $baseDomain . $newUrlPath;

            // Check if repair is needed
            if ($page->slug === $newSlug && $page->url_path === $newUrlPath) {
                $this->line("  <fg=gray>⊘</> {$page->slug} - Already correct, skipping");
                $skipped++;
                continue;
            }

            // Show before/after
            $this->line("  <fg=yellow>→</> {$page->county->name}:");
            $this->line("    <fg=red>BEFORE:</> {$page->slug}");
            $this->line("             {$page->url_path}");
            $this->line("    <fg=green>AFTER:</> {$newSlug}");
            $this->line("            {$newUrlPath}");

            if ($dryRun) {
                $this->line("    <fg=cyan>[DRY RUN]</> Would update record ID {$page->id}");
                $repaired++; // Count as "would be repaired"
                $this->newLine();
                continue;
            }

            // Check for slug conflicts
            $existingPage = LocationPage::where('slug', $newSlug)
                ->where('id', '!=', $page->id)
                ->first();

            if ($existingPage) {
                $this->error("    ✗ Cannot repair: slug '{$newSlug}' already exists (ID: {$existingPage->id})");
                $errors++;
                $this->newLine();
                continue;
            }

            // Perform the update using a transaction
            try {
                DB::transaction(function () use ($page, $newSlug, $newUrlPath, $newCanonicalUrl) {
                    $page->update([
                        'slug' => $newSlug,
                        'url_path' => $newUrlPath,
                        'canonical_url' => $newCanonicalUrl,
                    ]);
                });

                $this->line("    <fg=green>✓</> Updated successfully");
                $repaired++;
            } catch (\Exception $e) {
                $this->error("    ✗ Failed to update: {$e->getMessage()}");
                $errors++;
            }

            $this->newLine();
        }

        // Summary
        $this->line('═══════════════════════════════════════════');
        $this->info('Summary:');
        
        if ($dryRun) {
            $this->line("  Would repair: {$repaired}");
        } else {
            $this->line("  Repaired: <fg=green>{$repaired}</>");
        }
        
        $this->line("  Skipped (already correct): {$skipped}");
        
        if ($errors > 0) {
            $this->line("  Errors: <fg=red>{$errors}</>");
        }

        $this->newLine();

        if ($dryRun && $repaired > 0) {
            $this->warn('Run without --dry-run to apply changes');
        } elseif (!$dryRun && $repaired > 0) {
            $this->info('✓ Repair complete!');
            $this->line('Next steps:');
            $this->line('  1. Run: php artisan seo:test-location-pages');
            $this->line('  2. Verify preview URLs work correctly');
            $this->line('  3. Re-export if needed: php artisan seo:export-location-pages');
        }

        return $errors > 0 ? 1 : 0;
    }
}
