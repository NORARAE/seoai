<?php

namespace App\Services;

use App\Models\BaselineSnapshot;
use App\Models\City;
use App\Models\LocationPage;
use App\Models\Service;
use App\Models\ServiceLocation;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * LocationPageGeneratorService
 * 
 * Generates location pages from coverage opportunities
 * Integrates with CoverageMatrixService and LocationPageComposer
 */
class LocationPageGeneratorService
{
    public function __construct(
        protected LocationPageComposer $composer,
        protected InternalLinkPlannerService $linkPlanner,
        protected LocationPageValidationService $validator,
        protected LocationPageRenderService $renderer,
        protected CoverageMatrixService $coverageMatrix,
        protected PageUrlResolver $urlResolver
    ) {}

    /**
     * Generate a location page from a ServiceLocation opportunity
     */
    public function generateFromOpportunity(ServiceLocation $serviceLocation, Site $site): array
    {
        // Validation checks
        if ($serviceLocation->page_exists) {
            return [
                'success' => false,
                'error' => 'Page already exists for this service-location combination',
                'location_page_id' => $serviceLocation->location_page_id,
            ];
        }

        // Check for duplicate (extra safety)
        $existingPage = LocationPage::where('site_id', $site->id)
            ->where('service_id', $serviceLocation->service_id)
            ->where('city_id', $serviceLocation->city_id)
            ->first();

        if ($existingPage) {
            return [
                'success' => false,
                'error' => 'Duplicate page detected',
                'location_page_id' => $existingPage->id,
            ];
        }

        try {
            DB::beginTransaction();

            // Load relationships
            $service = Service::findOrFail($serviceLocation->service_id);
            $city = City::with(['county', 'state'])->findOrFail($serviceLocation->city_id);
            $state = $city->state;
            $county = $city->county;

            // Compose content
            $content = $this->composer->composeServiceCity($service, $city, $state);

            // Create location page
            $locationPage = LocationPage::create([
                'site_id' => $site->id,
                'client_id' => $site->client_id,
                'service_id' => $service->id,
                'state_id' => $state->id,
                'county_id' => $county->id,
                'city_id' => $city->id,
                'page_type' => 'service_city',
                'title' => $content['title'],
                'meta_title' => $content['meta_title'],
                'meta_description' => $content['meta_description'],
                'h1' => $content['h1'],
                'body_sections_json' => $content['body_sections_json'],
                'status' => 'published',
            ]);

            // Generate and set URL
            $url = $this->urlResolver->resolve($locationPage);
            $locationPage->url = $url;

            // Generate internal links
            $internalLinks = $this->linkPlanner->planLinks($locationPage);
            $locationPage->internal_links_json = $internalLinks;

            // Validate content
            $validationResult = $this->validator->validate($locationPage);
            $locationPage->validation_passed = $validationResult['passed'];
            $locationPage->validation_issues_json = $validationResult['issues'] ?? [];
            $locationPage->content_score = $validationResult['score'] ?? 0;

            // Render and cache
            $this->renderer->renderAndCache($locationPage);

            $locationPage->save();

            // Create baseline snapshot
            BaselineSnapshot::create([
                'site_id' => $site->id,
                'page_type' => LocationPage::class,
                'page_id' => $locationPage->id,
                'snapshot_date' => now(),
                'impressions' => 0,
                'clicks' => 0,
                'ctr' => 0,
                'position' => 0,
                'indexed' => false,
                'notes' => 'Initial baseline for newly generated page',
            ]);

            // Update service location
            $serviceLocation->page_exists = true;
            $serviceLocation->location_page_id = $locationPage->id;
            $serviceLocation->status = 'generated';
            $serviceLocation->last_analyzed_at = now();
            $serviceLocation->save();

            DB::commit();

            Log::info('Generated location page from opportunity', [
                'location_page_id' => $locationPage->id,
                'service_location_id' => $serviceLocation->id,
                'service' => $service->name,
                'city' => $city->name,
                'url' => $url,
            ]);

            return [
                'success' => true,
                'location_page_id' => $locationPage->id,
                'service_location_id' => $serviceLocation->id,
                'url' => $url,
                'validation_passed' => $validationResult['passed'],
                'content_score' => $validationResult['score'] ?? 0,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to generate location page', [
                'service_location_id' => $serviceLocation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate multiple pages from top opportunities
     */
    public function generateBatch(Site $site, int $count = 10, ?int $minPriorityScore = 70): array
    {
        $state = $site->state;

        $opportunities = ServiceLocation::where('state_id', $state->id)
            ->missingPages()
            ->where('priority_score', '>=', $minPriorityScore)
            ->orderBy('priority_score', 'desc')
            ->limit($count)
            ->get();

        $results = [
            'total' => $opportunities->count(),
            'successful' => 0,
            'failed' => 0,
            'pages' => [],
            'errors' => [],
        ];

        foreach ($opportunities as $opportunity) {
            $result = $this->generateFromOpportunity($opportunity, $site);

            if ($result['success']) {
                $results['successful']++;
                $results['pages'][] = $result['location_page_id'];
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'service_location_id' => $opportunity->id,
                    'error' => $result['error'],
                ];
            }
        }

        // Refresh coverage matrix after batch generation
        $this->coverageMatrix->buildMatrix($state);

        Log::info('Batch generated location pages', $results);

        return $results;
    }

    /**
     * Generate pages for specific service across all cities
     */
    public function generateForService(Site $site, Service $service, ?int $minPriorityScore = 60): array
    {
        $state = $site->state;

        $opportunities = ServiceLocation::where('state_id', $state->id)
            ->where('service_id', $service->id)
            ->missingPages()
            ->where('priority_score', '>=', $minPriorityScore)
            ->orderBy('priority_score', 'desc')
            ->get();

        $results = [
            'service' => $service->name,
            'total' => $opportunities->count(),
            'successful' => 0,
            'failed' => 0,
            'pages' => [],
            'errors' => [],
        ];

        foreach ($opportunities as $opportunity) {
            $result = $this->generateFromOpportunity($opportunity, $site);

            if ($result['success']) {
                $results['successful']++;
                $results['pages'][] = $result['location_page_id'];
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'service_location_id' => $opportunity->id,
                    'error' => $result['error'],
                ];
            }
        }

        // Refresh coverage matrix
        $this->coverageMatrix->buildMatrix($state, $service);

        Log::info('Generated location pages for service', $results);

        return $results;
    }

    /**
     * Generate pages for all cities in a specific county
     */
    public function generateForCounty(Site $site, int $countyId, ?int $minPriorityScore = 60): array
    {
        $state = $site->state;

        $opportunities = ServiceLocation::where('state_id', $state->id)
            ->where('county_id', $countyId)
            ->missingPages()
            ->where('priority_score', '>=', $minPriorityScore)
            ->orderBy('priority_score', 'desc')
            ->get();

        $results = [
            'county_id' => $countyId,
            'total' => $opportunities->count(),
            'successful' => 0,
            'failed' => 0,
            'pages' => [],
            'errors' => [],
        ];

        foreach ($opportunities as $opportunity) {
            $result = $this->generateFromOpportunity($opportunity, $site);

            if ($result['success']) {
                $results['successful']++;
                $results['pages'][] = $result['location_page_id'];
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'service_location_id' => $opportunity->id,
                    'error' => $result['error'],
                ];
            }
        }

        // Refresh coverage matrix
        $this->coverageMatrix->buildMatrix($state);

        Log::info('Generated location pages for county', $results);

        return $results;
    }
}
