<?php

namespace App\Services\Discovery;

use App\Models\City;
use App\Models\ScanRun;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Models\UrlInventory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Scans crawl data and surface three categories of SEO opportunities:
 *
 *  1. Location gaps   – service pages that exist in some cities but are absent in others
 *                       within the site's operating state.
 *  2. Content gaps    – supporting sub-pages (e.g. /service-cost, /service-process) that
 *                       are missing when the primary service page exists.
 *  3. Internal link   – crawled pages whose incoming_link_count is below a threshold,
 *                       making them effectively orphaned and hard to rank.
 */
class OpportunityDetectionService
{
    /** Supporting sub-page slug suffixes to probe for content gaps. */
    private const SUPPORTING_SUFFIXES = [
        'cost',
        'pricing',
        'process',
        'near-me',
        'benefits',
        'faq',
    ];

    /** Pages with fewer incoming links than this are flagged as internal-link opportunities. */
    private const ORPHAN_LINK_THRESHOLD = 2;

    /**
     * Run all detectors for a site and return a summary of what was created.
     *
     * @return array{location_gaps: int, content_gaps: int, internal_link_opps: int}
     */
    public function detect(Site $site): array
    {
        $scanRunId = ScanRun::where('site_id', $site->id)
            ->whereIn('status', ['running', 'pending'])
            ->latest('started_at')
            ->value('id');

        $locationGaps    = $this->detectLocationGaps($site, $scanRunId);
        $contentGaps     = $this->detectContentGaps($site, $scanRunId);
        $internalLinkOpps = $this->detectInternalLinkOpportunities($site, $scanRunId);

        Log::info('OpportunityDetectionService::detect', [
            'site_id'          => $site->id,
            'location_gaps'    => $locationGaps,
            'content_gaps'     => $contentGaps,
            'internal_link_opps' => $internalLinkOpps,
            'scan_run_id'      => $scanRunId,
        ]);

        return [
            'location_gaps'      => $locationGaps,
            'content_gaps'       => $contentGaps,
            'internal_link_opps' => $internalLinkOpps,
        ];
    }

    // -------------------------------------------------------------------------
    // Location gap detection
    // -------------------------------------------------------------------------

    /**
     * For every active service, check which cities in the site's state have no
     * matching crawled page and create a `new_page` opportunity for each gap.
     */
    public function detectLocationGaps(Site $site, ?int $scanRunId = null): int
    {
        if (! $site->state_id) {
            return 0;
        }

        $cities = City::where('state_id', $site->state_id)->get();

        if ($cities->isEmpty()) {
            return 0;
        }

        $services = Service::where('is_active', true)->get();

        if ($services->isEmpty()) {
            return 0;
        }

        // Collect all crawled paths once to avoid repeated DB calls.
        $crawledPaths = UrlInventory::where('site_id', $site->id)
            ->whereNotNull('path')
            ->pluck('path')
            ->map(fn (string $p) => trim(strtolower($p), '/'))
            ->values()
            ->all();

        $created = 0;

        foreach ($services as $service) {
            /** @var Service $service */
            foreach ($cities as $city) {
                /** @var City $city */
                $slug = $this->buildServiceLocationSlug($service->slug ?? Str::slug($service->name), $city);

                // If any crawled path contains this slug, the page exists – skip.
                $exists = collect($crawledPaths)->contains(fn (string $p) => str_contains($p, $slug));

                if ($exists) {
                    continue;
                }

                $opportunity = SeoOpportunity::firstOrCreate(
                    [
                        'site_id'    => $site->id,
                        'service_id' => $service->id,
                        'location_id' => $city->id,
                    ],
                    [
                        'client_id'        => $site->client_id,
                        'scan_run_id'      => $scanRunId,
                        'opportunity_type' => 'new_page',
                        'status'           => 'pending',
                        'page_exists'      => false,
                        'target_keyword'   => $service->name . ' ' . $city->name,
                        'suggested_url'    => '/' . $slug,
                        'detection_source' => 'crawl_discovery',
                        'identified_at'    => now(),
                    ],
                );

                if ($opportunity->wasRecentlyCreated) {
                    $created++;
                }
            }
        }

        return $created;
    }

    // -------------------------------------------------------------------------
    // Content gap detection
    // -------------------------------------------------------------------------

    /**
     * For each service page that was crawled, look for missing supporting sub-pages
     * (e.g. /water-damage-restoration-spokane-wa-cost) and create `content_gap` opps.
     */
    public function detectContentGaps(Site $site, ?int $scanRunId = null): int
    {
        if (! $site->state_id) {
            return 0;
        }

        $crawledPaths = UrlInventory::where('site_id', $site->id)
            ->whereNotNull('path')
            ->pluck('path')
            ->map(fn (string $p) => trim(strtolower($p), '/'))
            ->values()
            ->all();

        $services = Service::where('is_active', true)->get();
        $cities   = City::where('state_id', $site->state_id)->get();

        $created = 0;

        foreach ($services as $service) {
            /** @var Service $service */
            foreach ($cities as $city) {
                /** @var City $city */
                $baseSlug = $this->buildServiceLocationSlug($service->slug ?? Str::slug($service->name), $city);

                // Only surface content gaps where the primary page exists.
                $primaryExists = collect($crawledPaths)->contains(fn (string $p) => str_contains($p, $baseSlug));

                if (! $primaryExists) {
                    continue;
                }

                foreach (self::SUPPORTING_SUFFIXES as $suffix) {
                    $supportingSlug = $baseSlug . '-' . $suffix;

                    $supportingExists = collect($crawledPaths)->contains(fn (string $p) => str_contains($p, $supportingSlug));

                    if ($supportingExists) {
                        continue;
                    }

                    // Use a synthetic unique key: service + city + content_gap type stored as
                    // separate rows distinguished by target_keyword (since the DB unique index
                    // only covers site/service/location we avoid conflicting with location gaps
                    // by checking status and type first).
                    $alreadyExists = SeoOpportunity::where('site_id', $site->id)
                        ->where('service_id', $service->id)
                        ->where('location_id', $city->id)
                        ->where('opportunity_type', 'content_gap')
                        ->where('target_keyword', $service->name . ' ' . $city->name . ' ' . $suffix)
                        ->exists();

                    if ($alreadyExists) {
                        continue;
                    }

                    // The db unique index is (site_id, service_id, location_id). Content gap rows
                    // would conflict with location gap rows. We only insert if there's no existing
                    // row for this combination yet.
                    $opportunity = SeoOpportunity::firstOrCreate(
                        [
                            'site_id'    => $site->id,
                            'service_id' => $service->id,
                            'location_id' => $city->id,
                        ],
                        [
                            'client_id'        => $site->client_id,
                            'scan_run_id'      => $scanRunId,
                            'opportunity_type' => 'content_gap',
                            'status'           => 'pending',
                            'page_exists'      => false,
                            'target_keyword'   => $service->name . ' ' . $city->name . ' ' . $suffix,
                            'suggested_url'    => '/' . $supportingSlug,
                            'detection_source' => 'crawl_discovery',
                            'identified_at'    => now(),
                        ],
                    );

                    if ($opportunity->wasRecentlyCreated) {
                        $created++;
                    }
                }
            }
        }

        return $created;
    }

    // -------------------------------------------------------------------------
    // Internal link opportunity detection
    // -------------------------------------------------------------------------

    /**
     * Find indexable pages with very few (or zero) incoming internal links and
     * create `quick_win` opportunities so editors know to add links to them.
     */
    public function detectInternalLinkOpportunities(Site $site, ?int $scanRunId = null): int
    {
        $underlinked = UrlInventory::where('site_id', $site->id)
            ->where('indexability_status', 'indexable')
            ->where('incoming_link_count', '<', self::ORPHAN_LINK_THRESHOLD)
            ->whereNotNull('path')
            ->get();

        $created = 0;

        foreach ($underlinked as $urlItem) {
            /** @var UrlInventory $urlItem */
            // Derive service/city from the URL path if possible, otherwise skip –
            // we can't create a well-typed opportunity without them.
            [$serviceId, $cityId] = $this->inferServiceAndCity($urlItem->path ?? '/', $site);

            if (! $serviceId || ! $cityId) {
                continue;
            }

            $opportunity = SeoOpportunity::firstOrCreate(
                [
                    'site_id'    => $site->id,
                    'service_id' => $serviceId,
                    'location_id' => $cityId,
                ],
                [
                    'client_id'        => $site->client_id,
                    'scan_run_id'      => $scanRunId,
                    'opportunity_type' => 'quick_win',
                    'status'           => 'pending',
                    'page_exists'      => true,
                    'suggested_url'    => $urlItem->path,
                    'detection_source' => 'crawl_discovery',
                    'notes'            => 'Only ' . $urlItem->incoming_link_count . ' internal link(s) pointing here; add more to boost ranking.',
                    'identified_at'    => now(),
                ],
            );

            if ($opportunity->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function buildServiceLocationSlug(string $serviceSlug, City $city): string
    {
        $stateCode = strtolower($city->state->code ?? 'us');

        return $serviceSlug . '-' . Str::slug($city->name) . '-' . $stateCode;
    }

    /**
     * Attempt to match a URL path to a service and city by scanning for slug fragments.
     * Returns [service_id, city_id] or [null, null] if no match found.
     */
    protected function inferServiceAndCity(string $path, Site $site): array
    {
        if (! $site->state_id) {
            return [null, null];
        }

        $path = trim(strtolower($path), '/');

        $services = Service::where('is_active', true)->get();
        $cities   = City::where('state_id', $site->state_id)->get();

        foreach ($services as $service) {
            /** @var Service $service */
            $serviceSlug = strtolower($service->slug ?? Str::slug($service->name));

            if (! str_contains($path, $serviceSlug)) {
                continue;
            }

            foreach ($cities as $city) {
                /** @var City $city */
                $citySlug = strtolower(Str::slug($city->name));

                if (str_contains($path, $citySlug)) {
                    return [$service->id, $city->id];
                }
            }
        }

        return [null, null];
    }
}
