<?php

namespace App\Services;

use App\Models\City;
use App\Models\County;
use App\Models\LocationPage;
use App\Models\Service;
use App\Models\State;
use Illuminate\Support\Collection;

/**
 * Validates location page generation rules to ensure data integrity and prevent duplicates
 */
class LocationPageValidationService
{
    /**
     * Validate if a county hub can be generated
     *
     * @param County $county
     * @param State $state
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateCountyHub(County $county, State $state): array
    {
        $errors = [];

        // Verify county belongs to the state
        if ($county->state_id !== $state->id) {
            $errors[] = "County {$county->name} does not belong to state {$state->name}";
        }

        // Check for valid county data
        if (empty($county->name)) {
            $errors[] = "County name is required";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validate if a service-city page can be generated
     *
     * @param Service $service
     * @param City $city
     * @param State $state
     * @param County $county
     * @param LocationPage|null $countyHub
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateServiceCity(
        Service $service,
        City $city,
        State $state,
        County $county,
        ?LocationPage $countyHub
    ): array {
        $errors = [];

        // Verify service is active
        if (!$service->is_active) {
            $errors[] = "Service {$service->name} is not active";
        }

        // Verify city belongs to the correct state
        if ($city->state_id !== $state->id) {
            $errors[] = "City {$city->name} does not belong to state {$state->name}";
        }

        // Verify city belongs to the correct county
        if ($city->county_id !== $county->id) {
            $errors[] = "City {$city->name} does not belong to county {$county->name}";
        }

        // Verify county hub exists (parent page requirement)
        if (!$countyHub) {
            $errors[] = "No county hub page exists for {$county->name}. County hub must be generated first.";
        } elseif ($countyHub->type !== 'county_hub') {
            $errors[] = "Parent page is not a county hub";
        } elseif ($countyHub->county_id !== $county->id) {
            $errors[] = "County hub does not match city's county";
        }

        // Check for valid city data
        if (empty($city->name)) {
            $errors[] = "City name is required";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validate canonical URL consistency
     *
     * @param string $urlPath
     * @param string $canonicalUrl
     * @param string $baseDomain
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateCanonicalUrl(string $urlPath, string $canonicalUrl, string $baseDomain = 'https://example.com'): array
    {
        $errors = [];

        $expectedCanonical = $baseDomain . $urlPath;

        if ($canonicalUrl !== $expectedCanonical) {
            $errors[] = "Canonical URL mismatch. Expected: {$expectedCanonical}, Got: {$canonicalUrl}";
        }

        // Ensure URL path starts with /
        if (!str_starts_with($urlPath, '/')) {
            $errors[] = "URL path must start with /";
        }

        // Ensure URL path ends with /
        if (!str_ends_with($urlPath, '/')) {
            $errors[] = "URL path must end with /";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Check for potential duplicate pages by logical combination
     *
     * @param string $type 'county_hub' or 'service_city'
     * @param int $countyId
     * @param int|null $cityId
     * @param int|null $serviceId
     * @return array ['exists' => bool, 'page' => LocationPage|null]
     */
    public function checkDuplicateLogicalCombination(
        string $type,
        int $countyId,
        ?int $cityId = null,
        ?int $serviceId = null
    ): array {
        $query = LocationPage::where('type', $type)
            ->where('county_id', $countyId);

        if ($type === 'county_hub') {
            $query->whereNull('city_id')
                ->whereNull('service_id');
        } elseif ($type === 'service_city') {
            $query->where('city_id', $cityId)
                ->where('service_id', $serviceId);
        }

        $existingPage = $query->first();

        return [
            'exists' => $existingPage !== null,
            'page' => $existingPage,
        ];
    }

    /**
     * Validate parent-child relationship integrity
     *
     * @param LocationPage $childPage
     * @param LocationPage|null $parentPage
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateParentChildRelationship(LocationPage $childPage, ?LocationPage $parentPage): array
    {
        $errors = [];

        // County hubs should not have parents
        if ($childPage->type === 'county_hub' && $parentPage !== null) {
            $errors[] = "County hub pages should not have parent pages";
        }

        // Service-city pages must have parent county hub
        if ($childPage->type === 'service_city') {
            if ($parentPage === null) {
                $errors[] = "Service-city pages must have a parent county hub";
            } elseif ($parentPage->type !== 'county_hub') {
                $errors[] = "Service-city parent must be a county hub";
            } elseif ($parentPage->county_id !== $childPage->county_id) {
                $errors[] = "Service-city parent county hub must match child's county";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Run all validation checks for a county hub generation
     *
     * @param County $county
     * @param State $state
     * @param string $urlPath
     * @param string $canonicalUrl
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateCountyHubGeneration(
        County $county,
        State $state,
        string $urlPath,
        string $canonicalUrl
    ): array {
        $allErrors = [];

        // Basic validation
        $hubValidation = $this->validateCountyHub($county, $state);
        if (!$hubValidation['valid']) {
            $allErrors = array_merge($allErrors, $hubValidation['errors']);
        }

        // Canonical URL validation
        $canonicalValidation = $this->validateCanonicalUrl($urlPath, $canonicalUrl);
        if (!$canonicalValidation['valid']) {
            $allErrors = array_merge($allErrors, $canonicalValidation['errors']);
        }

        return [
            'valid' => empty($allErrors),
            'errors' => $allErrors,
        ];
    }

    /**
     * Run all validation checks for a service-city page generation
     *
     * @param Service $service
     * @param City $city
     * @param State $state
     * @param County $county
     * @param LocationPage|null $countyHub
     * @param string $urlPath
     * @param string $canonicalUrl
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateServiceCityGeneration(
        Service $service,
        City $city,
        State $state,
        County $county,
        ?LocationPage $countyHub,
        string $urlPath,
        string $canonicalUrl
    ): array {
        $allErrors = [];

        // Basic validation
        $serviceCityValidation = $this->validateServiceCity($service, $city, $state, $county, $countyHub);
        if (!$serviceCityValidation['valid']) {
            $allErrors = array_merge($allErrors, $serviceCityValidation['errors']);
        }

        // Canonical URL validation
        $canonicalValidation = $this->validateCanonicalUrl($urlPath, $canonicalUrl);
        if (!$canonicalValidation['valid']) {
            $allErrors = array_merge($allErrors, $canonicalValidation['errors']);
        }

        // Parent-child validation if county hub exists
        if ($countyHub) {
            $parentChildValidation = $this->validateParentChildRelationship(
                new LocationPage([
                    'type' => 'service_city',
                    'county_id' => $county->id,
                    'city_id' => $city->id,
                    'service_id' => $service->id,
                ]),
                $countyHub
            );
            if (!$parentChildValidation['valid']) {
                $allErrors = array_merge($allErrors, $parentChildValidation['errors']);
            }
        }

        return [
            'valid' => empty($allErrors),
            'errors' => $allErrors,
        ];
    }
}
