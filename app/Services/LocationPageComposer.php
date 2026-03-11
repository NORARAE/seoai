<?php

namespace App\Services;

use App\Models\City;
use App\Models\County;
use App\Models\Service;
use App\Models\State;

class LocationPageComposer
{
    /**
     * Compose content for a county hub page
     *
     * @param County $county
     * @param State $state
     * @return array
     */
    public function composeCountyHub(County $county, State $state): array
    {
        $countyName = $county->name;
        $stateCode = strtoupper($state->code);
        $stateName = $state->name;
        
        // Title pattern: "{County} County, {State Code} Service Area"
        $title = "{$countyName}, {$stateCode} Service Area";
        
        // Meta title (same as title for county hubs)
        $metaTitle = $title;
        
        // H1 (same as title for consistency)
        $h1 = $title;
        
        // Meta description
        $metaDescription = "Professional service coverage throughout {$countyName}, {$stateCode}. "
            . "Serving all cities and communities in the {$countyName} area with reliable, expert solutions.";
        
        // Enhanced body sections with more comprehensive content
        $bodySections = [
            [
                'type' => 'hero',
                'heading' => $h1,
                'content' => "We proudly serve all cities and communities throughout {$countyName}, {$stateCode}. "
                    . "Our team is dedicated to providing fast, professional service to every corner of the county.",
            ],
            [
                'type' => 'intro',
                'heading' => "Comprehensive Service Coverage",
                'content' => "As your trusted service provider in {$countyName}, we understand the unique needs "
                    . "of {$stateName} communities. Our local expertise combined with industry-leading standards "
                    . "ensures you receive the highest quality service, no matter where you are in the county.",
            ],
            [
                'type' => 'service_overview',
                'heading' => "Services Throughout {$countyName}",
                'content' => "Our comprehensive service offerings are available to all residents and businesses "
                    . "across {$countyName}. From the county seat to rural communities, we provide consistent, "
                    . "reliable service with rapid response times and professional execution.",
            ],
            [
                'type' => 'local_relevance',
                'heading' => "Why Choose Local {$countyName} Experts",
                'content' => "Working with a {$countyName}-based service provider means faster response times, "
                    . "local knowledge, and a team that understands {$stateName} regulations and community standards. "
                    . "We're your neighbors, and we're invested in the wellbeing of our community.",
            ],
            [
                'type' => 'coverage_area',
                'heading' => "Cities and Communities We Serve",
                'content' => "We serve all municipalities, unincorporated areas, and communities within {$countyName}, {$stateCode}. "
                    . "No location is too remote – if you're in {$countyName}, we're here to help.",
            ],
            [
                'type' => 'cta',
                'heading' => "Contact Us Today",
                'content' => "Need service in {$countyName}? Our team is standing by 24/7 to respond to your needs. "
                    . "Call us now for immediate assistance or schedule an appointment at your convenience.",
            ],
            [
                'type' => 'internal_links',
                'heading' => "Service Areas in {$countyName}",
                'content' => "Explore our service offerings in specific {$countyName} cities:",
                'note' => 'Links will be dynamically populated from internal_links_json',
            ],
        ];
        
        return [
            'title' => $title,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'h1' => $h1,
            'body_sections_json' => $bodySections,
        ];
    }

    /**
     * Compose content for a service-city page
     *
     * @param Service $service
     * @param City $city
     * @param State $state
     * @return array
     */
    public function composeServiceCity(Service $service, City $city, State $state): array
    {
        $serviceName = $service->name;
        $cityName = $city->name;
        $countyName = $city->county->name;
        $stateCode = strtoupper($state->code);
        $stateName = $state->name;
        
        // Title pattern: "{Service Name} in {City}, {State Code}"
        $title = "{$serviceName} in {$cityName}, {$stateCode}";
        
        // Meta title (optimized for SEO)
        $metaTitle = "{$serviceName} Services in {$cityName}, {$stateCode}";
        
        // H1
        $h1 = "{$serviceName} in {$cityName}";
        
        // Meta description
        $metaDescription = "Professional {$serviceName} services in {$cityName}, {$stateCode}. "
            . "Fast, reliable, and available 24/7. Serving {$countyName} and surrounding areas.";
        
        // Enhanced body sections with comprehensive content structure
        $bodySections = [
            [
                'type' => 'hero',
                'heading' => $h1,
                'content' => "When you need expert {$serviceName} services in {$cityName}, {$stateCode}, "
                    . "our certified team is ready to help. We provide fast, professional service with "
                    . "the highest standards of quality and care.",
            ],
            [
                'type' => 'intro',
                'heading' => "Professional {$serviceName} Services",
                'content' => "Our {$cityName}-area team specializes in {$serviceName}, bringing years of experience "
                    . "and industry-leading expertise to every job. We understand the specific needs of {$cityName} "
                    . "residents and businesses, and we're committed to exceeding your expectations.",
            ],
            [
                'type' => 'service_description',
                'heading' => "Comprehensive {$serviceName}",
                'content' => "Our {$serviceName} services include complete assessment, professional execution, "
                    . "and thorough follow-up. We use state-of-the-art equipment and proven methodologies to ensure "
                    . "the best possible outcomes for our {$cityName} clients.",
            ],
            [
                'type' => 'local_relevance',
                'heading' => "Local Expertise in {$cityName}",
                'content' => "As a locally-focused service provider, we understand {$cityName}'s unique characteristics, "
                    . "regulations, and community needs. Our team is familiar with {$countyName} requirements and "
                    . "{$stateName} standards, ensuring compliant, reliable service every time.",
            ],
            [
                'type' => 'county_support',
                'heading' => "Serving {$countyName}",
                'content' => "While we specialize in {$cityName}, our service coverage extends throughout {$countyName}. "
                    . "We're proud to serve communities across the county with the same level of professionalism and care "
                    . "that has made us the trusted choice for {$serviceName} in the region.",
            ],
            [
                'type' => 'availability',
                'heading' => "24/7 Emergency Service",
                'content' => "Emergencies don't wait for business hours. Our {$serviceName} team is available around "
                    . "the clock in {$cityName}, {$stateCode}. Call us anytime, day or night, for immediate assistance.",
            ],
            [
                'type' => 'cta',
                'heading' => "Get Help Now",
                'content' => "Don't wait when you need {$serviceName} in {$cityName}. Contact our team now for "
                    . "fast, professional service. We're standing by to help with your immediate needs or to schedule "
                    . "an appointment at your convenience.",
            ],
            [
                'type' => 'internal_links',
                'heading' => "Related Service Areas",
                'content' => "We also provide {$serviceName} services in nearby cities:",
                'note' => 'Links will be dynamically populated from internal_links_json',
            ],
        ];
        
        return [
            'title' => $title,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'h1' => $h1,
            'body_sections_json' => $bodySections,
        ];
    }
}
