<?php

namespace App\Services;

use App\Models\City;
use App\Models\LocationPage;
use Illuminate\Support\Collection;

/**
 * Plans internal linking strategies for location pages to improve SEO and site structure
 */
class InternalLinkPlannerService
{
    protected LocationIntelligenceService $locationIntelligence;

    public function __construct(LocationIntelligenceService $locationIntelligence)
    {
        $this->locationIntelligence = $locationIntelligence;
    }

    /**
     * Plan internal links for a county hub page
     * Links to all child service-city pages within the county
     *
     * @param LocationPage $countyHub
     * @return array Structure: ['links' => [['url' => string, 'anchor' => string, 'rel' => string]]]
     */
    public function planCountyHubLinks(LocationPage $countyHub): array
    {
        if ($countyHub->type !== 'county_hub') {
            return ['links' => []];
        }

        // Get all service-city pages that are children of this county hub
        $childPages = LocationPage::where('type', 'service_city')
            ->where('parent_location_page_id', $countyHub->id)
            ->where('status', '!=', 'archived')
            ->with(['city', 'service'])
            ->orderBy('score', 'desc')
            ->get();

        $links = [];

        foreach ($childPages as $childPage) {
            $links[] = [
                'url' => $childPage->url_path,
                'anchor' => $childPage->title,
                'rel' => 'child-page',
                'type' => 'service-city',
                'city_name' => $childPage->city?->name,
                'service_name' => $childPage->service?->name,
            ];
        }

        return [
            'links' => $links,
            'total_links' => count($links),
            'linked_services' => $childPages->pluck('service.name')->unique()->values()->toArray(),
            'linked_cities' => $childPages->pluck('city.name')->unique()->values()->toArray(),
        ];
    }

    /**
     * Plan internal links for a service-city page
     * Links to parent county hub and 2-4 nearby same-service cities
     *
     * @param LocationPage $serviceCityPage
     * @param int $nearbyLimit Maximum number of nearby city links (default 4)
     * @return array
     */
    public function planServiceCityLinks(LocationPage $serviceCityPage, int $nearbyLimit = 4): array
    {
        if ($serviceCityPage->type !== 'service_city') {
            return ['links' => []];
        }

        $links = [];

        // 1. Link to parent county hub (always first)
        if ($serviceCityPage->parent) {
            $links[] = [
                'url' => $serviceCityPage->parent->url_path,
                'anchor' => $serviceCityPage->parent->title,
                'rel' => 'parent-page',
                'type' => 'county-hub',
                'county_name' => $serviceCityPage->county?->name,
            ];
        }

        // 2. Find nearby cities with the same service
        if ($serviceCityPage->city && $serviceCityPage->service) {
            $nearbyCityLinks = $this->findNearbySameServicePages(
                $serviceCityPage->city,
                $serviceCityPage->service_id,
                $serviceCityPage->id,
                $nearbyLimit
            );

            $links = array_merge($links, $nearbyCityLinks);
        }

        return [
            'links' => $links,
            'total_links' => count($links),
            'has_parent_link' => $serviceCityPage->parent !== null,
            'nearby_cities_count' => count($links) - ($serviceCityPage->parent ? 1 : 0),
        ];
    }

    /**
     * Find nearby cities with the same service for internal linking
     *
     * @param City $sourceCity
     * @param int $serviceId
     * @param int $excludePageId Current page to exclude
     * @param int $limit
     * @return array
     */
    protected function findNearbySameServicePages(
        City $sourceCity,
        int $serviceId,
        int $excludePageId,
        int $limit = 4
    ): array {
        // Get nearby cities based on geographic proximity
        $nearbyCities = $this->locationIntelligence->getNearbyCities($sourceCity, $limit * 2);

        if ($nearbyCities->isEmpty()) {
            return [];
        }

        // Get the city IDs
        $nearbyCityIds = $nearbyCities->pluck('id')->toArray();

        // Find location pages for these cities with the same service
        $nearbyPages = LocationPage::where('type', 'service_city')
            ->where('service_id', $serviceId)
            ->whereIn('city_id', $nearbyCityIds)
            ->where('id', '!=', $excludePageId)
            ->where('status', '!=', 'archived')
            ->with(['city'])
            ->get();

        // Create a distance map for sorting
        $distanceMap = $nearbyCities->keyBy('id')->map(fn($city) => $city->distance ?? 999);

        // Sort pages by distance and limit
        $sortedPages = $nearbyPages->sortBy(function ($page) use ($distanceMap) {
            return $distanceMap->get($page->city_id, 999);
        })->take($limit);

        $links = [];

        foreach ($sortedPages as $nearbyPage) {
            $distance = $distanceMap->get($nearbyPage->city_id);

            $links[] = [
                'url' => $nearbyPage->url_path,
                'anchor' => $nearbyPage->title,
                'rel' => 'related-location',
                'type' => 'nearby-service-city',
                'city_name' => $nearbyPage->city?->name,
                'distance_miles' => $distance,
            ];
        }

        return $links;
    }

    /**
     * Plan links for any location page type (router method)
     *
     * @param LocationPage $page
     * @param int $nearbyLimit
     * @return array
     */
    public function planLinksForPage(LocationPage $page, int $nearbyLimit = 4): array
    {
        return match ($page->type) {
            'county_hub' => $this->planCountyHubLinks($page),
            'service_city' => $this->planServiceCityLinks($page, $nearbyLimit),
            default => ['links' => [], 'error' => 'Unknown page type'],
        };
    }

    /**
     * Batch plan links for multiple pages (optimized for bulk generation)
     *
     * @param Collection $pages Collection of LocationPage models
     * @param int $nearbyLimit
     * @return array Keyed by page ID
     */
    public function batchPlanLinks(Collection $pages, int $nearbyLimit = 4): array
    {
        $results = [];

        foreach ($pages as $page) {
            $results[$page->id] = $this->planLinksForPage($page, $nearbyLimit);
        }

        return $results;
    }
}
