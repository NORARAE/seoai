<?php

namespace App\Services\Seo\Urls;

use App\Services\Seo\Normalization\LocationKeyNormalizer;
use App\Services\Seo\Normalization\ServiceNameNormalizer;

class UrlPatternStrategy
{
    public function __construct(
        protected ServiceNameNormalizer $serviceNameNormalizer,
        protected LocationKeyNormalizer $locationKeyNormalizer,
    ) {
    }

    /**
     * Port of legacy candidate path ordering logic from WordPress audit helpers.
     *
     * @param  array<string, mixed>  $location
     * @param  array<string, array<string, mixed>>|null  $locationMap
     * @return array<int, string>
     */
    public function candidatePaths(string $serviceSlug, string $locationKey, array $location, ?array $locationMap = null): array
    {
        $serviceSlug = $this->serviceNameNormalizer->normalizeSlug($serviceSlug);
        $locationKey = $this->locationKeyNormalizer->normalizeLocationKey($locationKey);

        $type = strtolower((string) ($location['type'] ?? 'city'));
        $state = (string) ($location['state'] ?? 'WA');
        $city = (string) ($location['city'] ?? $location['label'] ?? $locationKey);

        $flat = "/{$serviceSlug}-{$locationKey}/";
        $paths = [];

        if ($type === 'neighborhood') {
            $parentKey = $this->locationKeyNormalizer->buildParentCityKey($city, $state);
            $hoodSlug = $this->stripParentSuffix($locationKey, $parentKey);
            $paths[] = "/{$serviceSlug}-{$parentKey}/{$hoodSlug}/";

            return $this->unique($paths);
        }

        if ($type === 'area') {
            $parentKey = $this->locationKeyNormalizer->buildParentCityKey($city, $state);
            $parentType = strtolower((string) ($locationMap[$parentKey]['type'] ?? ''));
            $nestable = in_array($parentType, ['city', 'town'], true);

            if ($nestable) {
                $areaSlug = $this->stripParentSuffix($locationKey, $parentKey);
                $paths[] = "/{$serviceSlug}-{$parentKey}/{$areaSlug}/";
            }

            $paths[] = $flat;

            return $this->unique($paths);
        }

        $paths[] = $flat;

        return $this->unique($paths);
    }

    protected function stripParentSuffix(string $key, string $parentKey): string
    {
        $suffix = '-' . $parentKey;

        if (str_ends_with($key, $suffix)) {
            $key = substr($key, 0, -strlen($suffix));
        }

        return $this->locationKeyNormalizer->normalizeLocationKey($key);
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    protected function unique(array $paths): array
    {
        return array_values(array_unique($paths));
    }
}
