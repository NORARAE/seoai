<?php

namespace App\Services;

use App\Models\City;
use App\Models\County;
use App\Models\Service;
use Illuminate\Support\Collection;

class LocationIntelligenceService
{
    /**
     * Get nearby cities based on geographic proximity
     *
     * @param City $city Source city
     * @param int $limit Maximum number of cities to return
     * @return Collection<City>
     */
    public function getNearbyCities(City $city, int $limit = 5): Collection
    {
        if (!$city->latitude || !$city->longitude) {
            return collect();
        }

        $sourceLat = (float) $city->latitude;
        $sourceLon = (float) $city->longitude;

        return City::where('id', '!=', $city->id)
            ->where('state_id', $city->state_id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($nearbyCity) use ($sourceLat, $sourceLon) {
                $nearbyCity->distance = $this->calculateDistance(
                    $sourceLat,
                    $sourceLon,
                    (float) $nearbyCity->latitude,
                    (float) $nearbyCity->longitude
                );
                return $nearbyCity;
            })
            ->sortBy('distance')
            ->take($limit);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in miles
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 3959; // miles

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Generate county hub page slug
     *
     * @param Service $service
     * @param County $county
     * @return string
     */
    public function getCountyHubSlug(Service $service, County $county): string
    {
        return $service->slug . '-' . $county->slug;
    }

    /**
     * Generate city page slug for a service
     *
     * @param Service $service
     * @param City $city
     * @return string
     */
    public function getCityPageSlug(Service $service, City $city): string
    {
        return $service->slug . '-' . $city->slug;
    }

    /**
     * Generate county page slug for a service
     *
     * @param Service $service
     * @param County $county
     * @return string
     */
    public function getCountyPageSlug(Service $service, County $county): string
    {
        return $service->slug . '-' . $county->slug . '-county';
    }

    /**
     * Get service cluster recommendations for a city
     *
     * @param City $city
     * @param Collection $services
     * @return Collection Service slugs for this city
     */
    public function getServiceClusterForCity(City $city, Collection $services): Collection
    {
        return $services->map(function ($service) use ($city) {
            return [
                'service' => $service->name,
                'slug' => $this->getCityPageSlug($service, $city),
                'city' => $city->name,
            ];
        });
    }
}
