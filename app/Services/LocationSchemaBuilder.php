<?php

namespace App\Services;

use App\Models\LocationPage;

/**
 * LocationSchemaBuilder
 * 
 * Generates structured data (Schema.org JSON-LD) for LocationPage records.
 * Produces FAQ, Service, and LocalBusiness schema suitable for SEO.
 */
class LocationSchemaBuilder
{
    /**
     * Generate all schema types for a location page
     *
     * @param LocationPage $page
     * @return array
     */
    public function generateAllSchemas(LocationPage $page): array
    {
        return [
            'faq_schema' => $this->generateFaqSchema($page),
            'service_schema' => $this->generateServiceSchema($page),
            'local_business_schema' => $this->generateLocalBusinessSchema($page),
        ];
    }

    /**
     * Generate FAQPage schema
     *
     * @param LocationPage $page
     * @return array|null
     */
    public function generateFaqSchema(LocationPage $page): ?array
    {
        // Look for FAQ section in body_sections_json
        $faqSection = null;
        if ($page->body_sections_json && is_array($page->body_sections_json)) {
            foreach ($page->body_sections_json as $section) {
                if (isset($section['type']) && $section['type'] === 'faq') {
                    $faqSection = $section;
                    break;
                }
            }
        }

        if (!$faqSection || empty($faqSection['items'])) {
            return null;
        }

        $mainEntity = [];
        foreach ($faqSection['items'] as $item) {
            if (isset($item['question']) && isset($item['answer'])) {
                $mainEntity[] = [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ];
            }
        }

        if (empty($mainEntity)) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Generate Service schema
     *
     * @param LocationPage $page
     * @return array|null
     */
    public function generateServiceSchema(LocationPage $page): ?array
    {
        if ($page->type !== 'service_city') {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $page->title ?? $page->h1,
            'description' => $page->meta_description,
        ];

        // Add service type if we have the service relationship
        if ($page->service) {
            $schema['serviceType'] = $page->service->name;
        }

        // Add area served
        if ($page->city) {
            $schema['areaServed'] = [
                '@type' => 'City',
                'name' => $page->city->name,
            ];

            if ($page->city->state) {
                $schema['areaServed']['containedIn'] = [
                    '@type' => 'State',
                    'name' => $page->city->state->name,
                ];
            }
        }

        // Add provider placeholder (can be enhanced with business info)
        $schema['provider'] = [
            '@type' => 'LocalBusiness',
            'name' => 'Your Business Name',
        ];

        return $schema;
    }

    /**
     * Generate LocalBusiness schema
     *
     * @param LocationPage $page
     * @return array|null
     */
    public function generateLocalBusinessSchema(LocationPage $page): ?array
    {
        // Only generate for service_city pages
        if ($page->type !== 'service_city') {
            return null;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'Your Business Name',
            'description' => $page->meta_description,
            'url' => $page->canonical_url,
        ];

        // Add address if we have city/state
        if ($page->city && $page->city->state) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'addressLocality' => $page->city->name,
                'addressRegion' => $page->city->state->code,
                'addressCountry' => 'US',
            ];

            // Add area served
            $schema['areaServed'] = [
                '@type' => 'City',
                'name' => $page->city->name,
                'containedIn' => [
                    '@type' => 'State',
                    'name' => $page->city->state->name,
                ],
            ];
        }

        // Add services offered if we have the service relationship
        if ($page->service) {
            $schema['hasOfferCatalog'] = [
                '@type' => 'OfferCatalog',
                'name' => 'Services',
                'itemListElement' => [
                    [
                        '@type' => 'Offer',
                        'itemOffered' => [
                            '@type' => 'Service',
                            'name' => $page->service->name,
                            'description' => $page->meta_description,
                        ],
                    ],
                ],
            ];
        }

        return $schema;
    }

    /**
     * Render schema as JSON-LD script tag
     *
     * @param array|null $schema
     * @return string
     */
    public function renderSchemaTag(?array $schema): string
    {
        if (!$schema) {
            return '';
        }

        $json = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }

    /**
     * Render all schemas as JSON-LD script tags
     *
     * @param LocationPage $page
     * @return string
     */
    public function renderAllSchemaTags(LocationPage $page): string
    {
        $schemas = $this->generateAllSchemas($page);
        $html = '';

        foreach ($schemas as $schema) {
            if ($schema) {
                $html .= $this->renderSchemaTag($schema);
            }
        }

        return $html;
    }
}
