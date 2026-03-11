<?php

namespace App\Console\Commands;

use App\Models\County;
use App\Models\LocationPage;
use App\Models\Service;
use App\Models\State;
use App\Services\InternalLinkPlannerService;
use App\Services\LocationPageComposer;
use App\Services\LocationPageScoreService;
use App\Services\LocationPageValidationService;
use App\Services\SeoSlugGenerator;
use Illuminate\Console\Command;

class GenerateWashingtonDrafts extends Command
{
    protected $signature = 'seo:generate-wa-drafts 
                            {--skip-validation : Skip validation checks (not recommended)}
                            {--skip-links : Skip internal link planning}
                            {--base-domain=https://example.com : Base domain for canonical URLs}';

    protected $description = 'Generate draft location pages for Washington state (county hubs and qualified service-city pages) with validation and internal linking';

    protected int $validationErrors = 0;
    protected int $validationWarnings = 0;

    public function handle(
        SeoSlugGenerator $slugGenerator,
        LocationPageScoreService $scoreService,
        LocationPageComposer $composer,
        LocationPageValidationService $validator,
        InternalLinkPlannerService $linkPlanner
    ): int {
        $this->info('Starting Washington location page generation...');
        $this->info('Base domain: ' . $this->option('base-domain'));
        $this->newLine();

        // Get Washington state
        $washington = State::where('code', 'WA')->first();

        if (!$washington) {
            $this->error('Washington state not found. Please run the LocationSeeder first.');
            return self::FAILURE;
        }

        // Get all active services
        $services = Service::active()->get();

        if ($services->isEmpty()) {
            $this->error('No active services found. Please run the LocationSeeder first.');
            return self::FAILURE;
        }

        $this->info("Found {$services->count()} active services");

        // Phase 1: Generate county hub pages
        $this->info('Phase 1: Generating county hub pages...');
        $countyHubsCreated = $this->generateCountyHubs($washington, $slugGenerator, $composer, $validator);

        $this->newLine();

        // Phase 2: Generate service-city pages for qualified cities
        $this->info('Phase 2: Generating service-city pages for qualified cities...');
        $serviceCityPagesCreated = $this->generateServiceCityPages(
            $washington,
            $services,
            $slugGenerator,
            $scoreService,
            $composer,
            $validator
        );

        $this->newLine();

        // Phase 3: Plan internal links (if not skipped)
        if (!$this->option('skip-links')) {
            $this->info('Phase 3: Planning internal links...');
            $this->planInternalLinks($linkPlanner);
        } else {
            $this->warn('Phase 3: Skipped (--skip-links flag)');
        }

        $this->newLine();

        // Summary
        $this->info('✓ Generation complete!');
        $this->table(
            ['Type', 'Count'],
            [
                ['County Hubs', $countyHubsCreated],
                ['Service-City Pages', $serviceCityPagesCreated],
                ['Total', $countyHubsCreated + $serviceCityPagesCreated],
            ]
        );

        if ($this->validationErrors > 0 || $this->validationWarnings > 0) {
            $this->newLine();
            $this->warn("Validation summary: {$this->validationErrors} errors, {$this->validationWarnings} warnings");
        }

        return self::SUCCESS;
    }

    /**
     * Generate county hub pages for all counties in the state
     *
     * @param State $state
     * @param SeoSlugGenerator $slugGenerator
     * @param LocationPageComposer $composer
     * @param LocationPageValidationService $validator
     * @return int Number of county hubs created
     */
    protected function generateCountyHubs(
        State $state,
        SeoSlugGenerator $slugGenerator,
        LocationPageComposer $composer,
        LocationPageValidationService $validator
    ): int {
        $counties = County::where('state_id', $state->id)->get();
        $created = 0;

        foreach ($counties as $county) {
            // Generate URL path and slug
            $urlPath = $slugGenerator->generateCountyHubPath($county->name, $state->code);
            $slug = $slugGenerator->generateSlugFromPath($urlPath);

            // Compose content
            $content = $composer->composeCountyHub($county, $state);

            // Generate canonical URL
            $canonicalUrl = $this->option('base-domain') . $urlPath;

            // Validate before generation (unless skipped)
            if (!$this->option('skip-validation')) {
                $validation = $validator->validateCountyHubGeneration(
                    $county,
                    $state,
                    $urlPath,
                    $canonicalUrl
                );

                if (!$validation['valid']) {
                    $this->validationErrors++;
                    foreach ($validation['errors'] as $error) {
                        $this->error("  ✗ Validation error for {$county->name}: {$error}");
                    }
                    continue; // Skip this county
                }
            }

            // Create or update county hub page
            $locationPage = LocationPage::updateOrCreate(
                [
                    'type' => 'county_hub',
                    'county_id' => $county->id,
                    'city_id' => null,
                    'service_id' => null,
                ],
                [
                    'state_id' => $state->id,
                    'parent_location_page_id' => null,
                    'slug' => $slug,
                    'url_path' => $urlPath,
                    'title' => $content['title'],
                    'meta_title' => $content['meta_title'],
                    'meta_description' => $content['meta_description'],
                    'h1' => $content['h1'],
                    'canonical_url' => $canonicalUrl,
                    'body_sections_json' => $content['body_sections_json'],
                    'internal_links_json' => null, // Will be populated in Phase 3
                    'score' => null,
                    'status' => 'draft',
                    'is_indexable' => true,
                    'generated_at' => now(),
                ]
            );

            if ($locationPage->wasRecentlyCreated) {
                $created++;
                $this->line("  ✓ Created: {$county->name} → {$urlPath}");
            } else {
                $this->line("  ↻ Updated: {$county->name} → {$urlPath}");
            }
        }

        return $created;
    }

    /**
     * Generate service-city pages for qualified cities
     *
     * @param State $state
     * @param \Illuminate\Support\Collection $services
     * @param SeoSlugGenerator $slugGenerator
     * @param LocationPageScoreService $scoreService
     * @param LocationPageComposer $composer
     * @param LocationPageValidationService $validator
     * @return int Number of service-city pages created
     */
    protected function generateServiceCityPages(
        State $state,
        $services,
        SeoSlugGenerator $slugGenerator,
        LocationPageScoreService $scoreService,
        LocationPageComposer $composer,
        LocationPageValidationService $validator
    ): int {
        // Get qualified cities (with proximity bonus enabled by default)
        $qualifiedCities = $scoreService->getQualifiedCities($state->id, true);

        $this->info("Found {$qualifiedCities->count()} cities meeting score threshold");

        $created = 0;

        foreach ($qualifiedCities as $city) {
            $cityScore = $scoreService->calculateCityScore($city, true);

            // Get score breakdown for transparency
            $scoreBreakdown = $scoreService->getScoreBreakdown($city, true);

            // Get the county hub for this city (parent page)
            $countyHub = LocationPage::countyHub()
                ->where('county_id', $city->county_id)
                ->first();

            if (!$countyHub) {
                $this->warn("  ⚠ No county hub found for {$city->name} (county: {$city->county->name})");
                $this->validationWarnings++;
                continue;
            }

            foreach ($services as $service) {
                // Generate URL path and slug
                $urlPath = $slugGenerator->generateServiceCityPath($service->name, $city->name, $state->code);
                $slug = $slugGenerator->generateSlugFromPath($urlPath);

                // Compose content
                $content = $composer->composeServiceCity($service, $city, $state);

                // Generate canonical URL
                $canonicalUrl = $this->option('base-domain') . $urlPath;

                // Validate before generation (unless skipped)
                if (!$this->option('skip-validation')) {
                    $validation = $validator->validateServiceCityGeneration(
                        $service,
                        $city,
                        $state,
                        $city->county,
                        $countyHub,
                        $urlPath,
                        $canonicalUrl
                    );

                    if (!$validation['valid']) {
                        $this->validationErrors++;
                        foreach ($validation['errors'] as $error) {
                            $this->error("  ✗ Validation error for {$city->name} / {$service->name}: {$error}");
                        }
                        continue; // Skip this service-city combination
                    }
                }

                // Create or update service-city page
                $locationPage = LocationPage::updateOrCreate(
                    [
                        'type' => 'service_city',
                        'county_id' => $city->county_id,
                        'city_id' => $city->id,
                        'service_id' => $service->id,
                    ],
                    [
                        'state_id' => $state->id,
                        'parent_location_page_id' => $countyHub->id,
                        'slug' => $slug,
                        'url_path' => $urlPath,
                        'title' => $content['title'],
                        'meta_title' => $content['meta_title'],
                        'meta_description' => $content['meta_description'],
                        'h1' => $content['h1'],
                        'canonical_url' => $canonicalUrl,
                        'body_sections_json' => $content['body_sections_json'],
                        'internal_links_json' => null, // Will be populated in Phase 3
                        'score' => $cityScore,
                        'status' => 'draft',
                        'is_indexable' => true,
                        'generated_at' => now(),
                    ]
                );

                if ($locationPage->wasRecentlyCreated) {
                    $created++;
                }
            }

            // Show city summary with score breakdown
            $breakdown = "pop:{$scoreBreakdown['components']['population']} + " .
                "seat:{$scoreBreakdown['components']['county_seat']} + " .
                "pri:{$scoreBreakdown['components']['priority']} + " .
                "prox:{$scoreBreakdown['components']['proximity']}";
            
            $this->line("  ✓ {$city->name} (score: {$cityScore} = {$breakdown}): {$services->count()} pages");
        }

        return $created;
    }

    /**
     * Plan internal links for all location pages
     *
     * @param InternalLinkPlannerService $linkPlanner
     * @return void
     */
    protected function planInternalLinks(InternalLinkPlannerService $linkPlanner): void
    {
        // Get all location pages (both county hubs and service-city)
        $allPages = LocationPage::with(['city', 'service', 'county', 'parent'])
            ->where('status', '!=', 'archived')
            ->get();

        $this->info("Planning internal links for {$allPages->count()} pages...");

        $updated = 0;

        foreach ($allPages as $page) {
            $linkPlan = $linkPlanner->planLinksForPage($page, 4);

            // Update the page with planned links
            $page->update([
                'internal_links_json' => $linkPlan,
            ]);

            $updated++;

            $linkCount = $linkPlan['total_links'] ?? 0;
            $this->line("  ✓ {$page->type}: {$page->title} → {$linkCount} links planned");
        }

        $this->info("Updated {$updated} pages with internal link plans");
    }
}
