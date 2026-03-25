<?php

namespace App\Services\Seo\Urls;

use App\Services\Seo\Normalization\LocationKeyNormalizer;
use App\Services\Seo\Normalization\ServiceNameNormalizer;

class CandidatePathBuilder
{
    public function __construct(
        protected UrlPatternStrategy $urlPatternStrategy,
        protected ServiceNameNormalizer $serviceNameNormalizer,
        protected LocationKeyNormalizer $locationKeyNormalizer,
    ) {
    }

    /**
     * Build prioritized relative candidate paths.
     *
     * @param  array<string, mixed>  $location
     * @param  array<string, array<string, mixed>>|null  $locationMap
     * @return array<int, string>
     */
    public function buildPrioritized(string $serviceSlug, string $locationKey, array $location, ?array $locationMap = null): array
    {
        return $this->urlPatternStrategy->candidatePaths($serviceSlug, $locationKey, $location, $locationMap);
    }

    /**
     * Port of legacy service-city URL token strategy.
     */
    public function buildServiceCityPath(
        string $serviceSlug,
        string $cityName,
        string $stateAbbreviation,
        string $pattern = '/%service%-%city%-%state%/'
    ): string {
        $service = $this->serviceNameNormalizer->normalizeSlug($serviceSlug);
        $city = $this->locationKeyNormalizer->normalizeCitySlug($cityName);
        $state = $this->locationKeyNormalizer->normalizeStateCode($stateAbbreviation);

        $path = str_replace(
            ['%service%', '%city%', '%state%'],
            [$service, $city, $state],
            $pattern
        );

        $path = '/' . ltrim($path, '/');

        return rtrim($path, '/') . '/';
    }
}
