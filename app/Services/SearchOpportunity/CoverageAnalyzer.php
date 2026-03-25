<?php

namespace App\Services\SearchOpportunity;

use App\Models\City;
use App\Models\LocationPage;
use App\Models\PageMetadata;
use App\Models\PagePayload;
use App\Models\PerformanceMetric;
use App\Models\Service;
use App\Models\Site;
use App\Models\UrlInventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CoverageAnalyzer
{
    public function analyze(Site $site): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Service> $services */
        $services = Service::query()->where('is_active', true)->orderBy('name')->get();

        /** @var \Illuminate\Database\Eloquent\Collection<int, City> $cities */
        $cities = City::query()
            ->where('state_id', $site->state_id)
            ->with('state')
            ->orderByDesc('is_priority')
            ->orderByDesc('population')
            ->orderBy('name')
            ->get();

        $payloads = PagePayload::query()
            ->where('site_id', $site->id)
            ->whereIn('status', ['ready', 'published'])
            ->whereIn('publish_status', ['pending', 'published', 'exported'])
            ->get()
            ->groupBy(fn (PagePayload $payload) => $this->combinationKey($payload->service_id, $payload->location_id));

        $locationPages = LocationPage::query()
            ->where('state_id', $site->state_id)
            ->whereNotNull('service_id')
            ->whereNotNull('city_id')
            ->get()
            ->groupBy(fn (LocationPage $page) => $this->combinationKey($page->service_id, $page->city_id));

        $discoveries = UrlInventory::query()
            ->where('site_id', $site->id)
            ->with('metadata')
            ->get();

        $performanceByUrl = PerformanceMetric::query()
            ->where('site_id', $site->id)
            ->where('date', '>=', now()->subDays(30))
            ->selectRaw('url, SUM(impressions) as impressions, SUM(clicks) as clicks, AVG(average_position) as average_position')
            ->groupBy('url')
            ->get()
            ->keyBy(fn ($metric) => $this->normalizeUrl($metric->url));

        $discoveryIndex = $discoveries->keyBy(fn (UrlInventory $url) => $this->normalizeUrl($url->url));

        $facts = collect();

        foreach ($services as $service) {
            /** @var Service $service */

            foreach ($cities as $city) {
                /** @var City $city */
                $combinationKey = $this->combinationKey($service->id, $city->id);
                $payload = $payloads->get($combinationKey)?->first();
                $locationPage = $locationPages->get($combinationKey)?->first();
                $expectedUrl = $this->expectedUrl($site, $service, $city);
                $normalizedExpectedUrl = $this->normalizeUrl($expectedUrl);
                $urlInventory = $discoveryIndex->get($normalizedExpectedUrl);
                $metadata = $urlInventory?->metadata;
                $performance = $performanceByUrl->get($normalizedExpectedUrl);

                $facts->push([
                    'site' => $site,
                    'service' => $service,
                    'city' => $city,
                    'expected_url' => $expectedUrl,
                    'payload' => $payload,
                    'location_page' => $locationPage,
                    'url_inventory' => $urlInventory,
                    'metadata' => $metadata,
                    'performance' => $performance,
                    'page_exists' => $payload !== null || $locationPage !== null || $urlInventory !== null,
                    'has_payload' => $payload !== null,
                    'has_location_page' => $locationPage !== null,
                    'has_discovered_url' => $urlInventory !== null,
                    'coverage_confidence' => $this->coverageConfidence($payload, $locationPage, $urlInventory),
                    'signals' => $this->buildSignals($city, $payload, $locationPage, $urlInventory, $metadata, $performance),
                ]);
            }
        }

        return $facts;
    }

    protected function buildSignals(
        City $city,
        ?PagePayload $payload,
        ?LocationPage $locationPage,
        ?UrlInventory $urlInventory,
        ?PageMetadata $metadata,
        mixed $performance,
    ): array {
        $schema = $metadata?->schema ?? [];

        return [
            'city_population' => (int) ($city->population ?? 0),
            'city_is_priority' => (bool) ($city->is_priority ?? false),
            'payload_seo_score' => $payload?->seo_score !== null ? (float) $payload->seo_score : null,
            'payload_content_quality_score' => $payload?->content_quality_score !== null ? (float) $payload->content_quality_score : null,
            'payload_schema_type' => $payload?->structured_data_type,
            'location_page_score' => $locationPage?->score,
            'url_depth' => $urlInventory?->depth,
            'word_count' => $urlInventory?->word_count,
            'incoming_link_count' => $urlInventory?->incoming_link_count,
            'internal_link_count' => $urlInventory?->internal_link_count,
            'is_orphan_page' => (bool) ($urlInventory?->is_orphan_page ?? false),
            'indexability_status' => $urlInventory?->indexability_status,
            'title_present' => filled($metadata?->title),
            'meta_description_present' => filled($metadata?->meta_description),
            'h1_present' => filled($metadata?->h1),
            'schema_present' => ! empty($schema),
            'schema_types' => collect($schema)
                ->map(fn ($item) => is_array($item) ? ($item['@type'] ?? null) : null)
                ->filter()
                ->values()
                ->all(),
            'impressions_30d' => $performance ? (int) $performance->impressions : 0,
            'clicks_30d' => $performance ? (int) $performance->clicks : 0,
            'average_position_30d' => $performance && $performance->average_position !== null ? (float) $performance->average_position : null,
            'ctr_30d' => $performance && (int) $performance->impressions > 0
                ? round(((int) $performance->clicks / max(1, (int) $performance->impressions)) * 100, 2)
                : 0.0,
        ];
    }

    protected function coverageConfidence(?PagePayload $payload, ?LocationPage $locationPage, ?UrlInventory $urlInventory): int
    {
        $score = 0;

        if ($payload) {
            $score += 40;
        }

        if ($locationPage) {
            $score += 35;
        }

        if ($urlInventory) {
            $score += 25;
        }

        return min(100, $score);
    }

    protected function expectedUrl(Site $site, Service $service, City $city): string
    {
        $domain = rtrim((string) $site->domain, '/');

        if (! Str::startsWith($domain, ['http://', 'https://'])) {
            $domain = 'https://' . $domain;
        }

        return $domain . '/' . $service->slug . '-' . $city->slug;
    }

    protected function combinationKey(?int $serviceId, ?int $cityId): string
    {
        return sprintf('%s:%s', $serviceId ?? 'none', $cityId ?? 'none');
    }

    protected function normalizeUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return rtrim(Str::lower($url), '/');
    }
}
