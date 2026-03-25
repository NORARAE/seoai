<?php

namespace App\Services\Seo\Normalization;

class LocationKeyNormalizer
{
    /**
     * @var array<string, string>
     */
    protected array $stateAbbreviationMap = [
        'washington' => 'wa',
        'oregon' => 'or',
        'idaho' => 'id',
        'arizona' => 'az',
        'nevada' => 'nv',
        'california' => 'ca',
    ];

    public function normalizeLocationKey(string $key): string
    {
        $normalized = strtolower(trim($key));
        $normalized = str_replace(['_', ' '], '-', $normalized);
        $normalized = preg_replace('/[^a-z0-9-]/', '', $normalized) ?? $normalized;
        $normalized = preg_replace('/-+/', '-', $normalized) ?? $normalized;

        return trim($normalized, '-');
    }

    public function normalizeCitySlug(string $cityName): string
    {
        return $this->normalizeLocationKey($cityName);
    }

    public function normalizeCountySlug(string $countyName): string
    {
        $normalized = $this->normalizeLocationKey($countyName);
        $normalized = preg_replace('/-county$/', '', $normalized) ?? $normalized;

        return trim($normalized, '-');
    }

    public function normalizeStateCode(string $state): string
    {
        $normalized = strtolower(trim($state));
        $normalized = preg_replace('/[^a-z]/', '', $normalized) ?? $normalized;

        if (strlen($normalized) === 2) {
            return $normalized;
        }

        return $this->stateAbbreviationMap[$normalized] ?? substr($normalized, 0, 2);
    }

    public function buildParentCityKey(string $cityName, string $state = 'WA'): string
    {
        return $this->normalizeCitySlug($cityName) . '-' . $this->normalizeStateCode($state);
    }
}
