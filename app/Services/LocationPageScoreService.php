<?php

namespace App\Services;

use App\Models\City;

class LocationPageScoreService
{
    /**
     * Score threshold to qualify for page generation
     */
    public const GENERATION_THRESHOLD = 50;

    protected LocationIntelligenceService $locationIntelligence;

    public function __construct(LocationIntelligenceService $locationIntelligence)
    {
        $this->locationIntelligence = $locationIntelligence;
    }

    /**
     * Calculate a score for a city to determine if it qualifies for service-city page generation
     *
     * Scoring rules:
     * - population >100000 = +40
     * - population 50000-99999 = +30
     * - population 20000-49999 = +20
     * - population 10000-19999 = +10
     * - county seat = +15
     * - is_priority = +20
     * - proximity bonus (optional if lat/lng available):
     *   - within 10 miles of county seat = +10
     *   - within 10 miles of priority city = +5
     *
     * @param City $city
     * @param bool $includeProximityBonus Whether to calculate proximity bonus (requires lat/lng)
     * @return int
     */
    public function calculateCityScore(City $city, bool $includeProximityBonus = true): int
    {
        $score = 0;

        // Population-based scoring
        if ($city->population !== null) {
            if ($city->population > 100000) {
                $score += 40;
            } elseif ($city->population >= 50000) {
                $score += 30;
            } elseif ($city->population >= 20000) {
                $score += 20;
            } elseif ($city->population >= 10000) {
                $score += 10;
            }
        }

        // County seat bonus
        if ($city->is_county_seat) {
            $score += 15;
        }

        // Priority flag bonus
        if ($city->is_priority) {
            $score += 20;
        }

        // Proximity bonus (only if lat/lng available and feature enabled)
        if ($includeProximityBonus && $city->latitude && $city->longitude) {
            $score += $this->calculateProximityBonus($city);
        }

        return $score;
    }

    /**
     * Calculate proximity bonus based on distance to important cities
     *
     * @param City $city
     * @return int
     */
    protected function calculateProximityBonus(City $city): int
    {
        $bonus = 0;

        // Get county seat and priority cities in the same state
        $importantCities = City::where('state_id', $city->state_id)
            ->where('id', '!=', $city->id)
            ->where(function ($query) {
                $query->where('is_county_seat', true)
                    ->orWhere('is_priority', true);
            })
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        foreach ($importantCities as $importantCity) {
            $distance = $this->locationIntelligence->calculateDistance(
                (float) $city->latitude,
                (float) $city->longitude,
                (float) $importantCity->latitude,
                (float) $importantCity->longitude
            );

            // Within 10 miles of county seat
            if ($distance <= 10 && $importantCity->is_county_seat) {
                $bonus = max($bonus, 10);
            }

            // Within 10 miles of priority city
            if ($distance <= 10 && $importantCity->is_priority) {
                $bonus = max($bonus, 5);
            }
        }

        return $bonus;
    }

    /**
     * Check if a city meets the threshold for page generation
     *
     * @param City $city
     * @param bool $includeProximityBonus
     * @return bool
     */
    public function meetsThreshold(City $city, bool $includeProximityBonus = true): bool
    {
        return $this->calculateCityScore($city, $includeProximityBonus) >= self::GENERATION_THRESHOLD;
    }

    /**
     * Get all cities that meet the generation threshold for a given state
     *
     * @param int $stateId
     * @param bool $includeProximityBonus
     * @return \Illuminate\Support\Collection
     */
    public function getQualifiedCities(int $stateId, bool $includeProximityBonus = true): \Illuminate\Support\Collection
    {
        return City::where('state_id', $stateId)
            ->get()
            ->filter(fn($city) => $this->meetsThreshold($city, $includeProximityBonus));
    }

    /**
     * Get score breakdown for debugging/transparency
     *
     * @param City $city
     * @param bool $includeProximityBonus
     * @return array
     */
    public function getScoreBreakdown(City $city, bool $includeProximityBonus = true): array
    {
        $breakdown = [
            'city_name' => $city->name,
            'population' => $city->population,
            'components' => [],
            'total_score' => 0,
            'meets_threshold' => false,
        ];

        // Population score
        $populationScore = 0;
        if ($city->population !== null) {
            if ($city->population > 100000) {
                $populationScore = 40;
            } elseif ($city->population >= 50000) {
                $populationScore = 30;
            } elseif ($city->population >= 20000) {
                $populationScore = 20;
            } elseif ($city->population >= 10000) {
                $populationScore = 10;
            }
        }
        $breakdown['components']['population'] = $populationScore;

        // County seat bonus
        $countySeatScore = $city->is_county_seat ? 15 : 0;
        $breakdown['components']['county_seat'] = $countySeatScore;

        // Priority bonus
        $priorityScore = $city->is_priority ? 20 : 0;
        $breakdown['components']['priority'] = $priorityScore;

        // Proximity bonus
        $proximityScore = 0;
        if ($includeProximityBonus && $city->latitude && $city->longitude) {
            $proximityScore = $this->calculateProximityBonus($city);
        }
        $breakdown['components']['proximity'] = $proximityScore;

        // Total
        $breakdown['total_score'] = $populationScore + $countySeatScore + $priorityScore + $proximityScore;
        $breakdown['meets_threshold'] = $breakdown['total_score'] >= self::GENERATION_THRESHOLD;
        $breakdown['threshold'] = self::GENERATION_THRESHOLD;

        return $breakdown;
    }
}
