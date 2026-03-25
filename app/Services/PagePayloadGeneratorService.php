<?php

namespace App\Services;

use App\Models\City;
use App\Models\County;
use App\Models\PageGenerationBatch;
use App\Models\PagePayload;
use App\Models\SeoOpportunity;
use App\Models\Service;
use App\Models\Site;
use App\Models\State;
use Illuminate\Support\Str;

/**
 * PagePayloadGeneratorService
 * 
 * Generates CMS-agnostic page payloads from SEO opportunities.
 * This is the core content generation service for the platform-agnostic architecture.
 * 
 * Responsibilities:
 * - Generate normalized page payloads
 * - Populate content fields (title, body, meta, schema)
 * - Calculate SEO scores
 * - Generate internal linking suggestions
 *- Apply best practices regardless of target CMS
 */
class PagePayloadGeneratorService
{
    public function __construct(
        protected LocationPageComposer $composer,
        protected LocationSchemaBuilder $schemaBuilder,
        protected InternalLinkPlannerService $linkPlanner,
    ) {}

    /**
     * Generate a PagePayload from an SEO opportunity
     * 
     * @param SeoOpportunity $opportunity
     * @param PageGenerationBatch|null $batch
     * @return PagePayload
     */
    public function generateFromOpportunity(
        SeoOpportunity $opportunity,
        ?PageGenerationBatch $batch = null
    ): PagePayload {
        $site = $opportunity->site;
        $service = $opportunity->service;
        $location = $this->resolveLocation($opportunity);
        $state = $site->state;

        // Generate content using composer
        $content = $this->generateContent($service, $location, $state);
        
        // Generate slug and URL
        $slug = $this->generateSlug($service, $location);
        
        // Generate schema
        $schema = $this->generateSchema($service, $location, $content);
        
        // Plan internal links
        $linkSuggestions = $this->planInternalLinks($site, $service, $location);
        
        // Generate body content
        $bodyContent = $this->renderBodyContent($content['body_sections_json'] ?? []);
        $excerpt = $this->generateExcerpt($content['body_sections_json'] ?? []);

        // Validate content was generated successfully
        if (empty($bodyContent)) {
            throw new \RuntimeException(
                "Body content generation produced empty result for opportunity #{$opportunity->id}. " .
                "Location: {$location->name}, Service: {$service->name}"
            );
        }
        
        // Create or update payload
        return PagePayload::updateOrCreate(
            [
                'site_id' => $site->id,
                'slug' => $slug,
            ],
            [
                'client_id' => $site->client_id,
                'batch_id' => $batch?->id,
                'service_id' => $service->id,
                'location_id' => $location->id,
                'location_type' => $opportunity->location_type ?? $this->getLocationTypeName($location),
                
                // Core content
                'title' => $content['title'] ?? '',
                'meta_description' => $content['meta_description'] ?? '',
                'slug' => $slug,
                'canonical_url_suggestion' => $this->generateCanonicalUrl($site, $slug),
                'body_content' => $bodyContent,
                'excerpt' => $excerpt,
                
                // SEO assets
                'schema_json_ld' => $schema,
                'structured_data_type' => 'LocalBusiness',
                'og_tags' => $this->generateOgTags($content, $site),
                
                // Linking strategy
                'internal_link_suggestions' => $linkSuggestions['links'] ?? null,
                'anchor_text_suggestions' => $linkSuggestions['anchors'] ?? null,
                
                // Hierarchy
                'parent_page_slug' => $linkSuggestions['parent_slug'] ?? null,
                'hub_page_slug' => $linkSuggestions['hub_slug'] ?? null,
                'related_pages' => $linkSuggestions['related'] ?? null,
                'submenu_suggestions' => $this->generateSubmenuSuggestions($service, $location),
                
                // Sitemap metadata
                'sitemap_priority' => $this->calculateSitemapPriority($opportunity),
                'sitemap_changefreq' => 'monthly',
                'sitemap_lastmod' => now(),
                
                // Publishing metadata
                'publish_notes' => 'Generated from SEO opportunity #' . $opportunity->id,
                'publish_status' => 'pending',
                'status' => 'needs_review',
                
                // Quality scores
                'content_quality_score' => $this->assessContentQuality($content),
                'seo_score' => $this->calculateSeoScore($content, $schema, $linkSuggestions),
                
                // Generation metadata
                'generated_by' => self::class,
                'generation_params' => [
                    'opportunity_id' => $opportunity->id,
                    'opportunity_type' => $opportunity->opportunity_type,
                    'priority_score' => $opportunity->priority_score,
                    'search_volume' => $opportunity->search_volume,
                ],
                'template_used' => 'service_location_v1',
            ]
        );
    }

    /**
     * Resolve location model from opportunity
     */
    protected function resolveLocation(SeoOpportunity $opportunity): City|County|State
    {
        return match($opportunity->location_type ?? 'city') {
            'city' => City::findOrFail($opportunity->location_id),
            'county' => County::findOrFail($opportunity->location_id),
            'state' => State::findOrFail($opportunity->location_id),
            default => City::findOrFail($opportunity->location_id),
        };
    }

    /**
     * Get location type class name
     */
    protected function getLocationTypeName($location): string
    {
        return match(true) {
            $location instanceof City => 'city',
            $location instanceof County => 'county',
            $location instanceof State => 'state',
            default => 'city',
        };
    }

    /**
     * Generate content using LocationPageComposer
     */
    protected function generateContent(Service $service, City|County|State $location, State $state): array
    {
        if ($location instanceof City) {
            $content = $this->composer->composeServiceCity($service, $location, $state);
        } elseif ($location instanceof County) {
            $content = $this->composer->composeCountyHub($location, $state);
        } else {
            // State-level content (future)
            $content = [
                'title' => "{$service->name} in {$location->name}",
                'meta_title' => "{$service->name} Services in {$location->name}",
                'meta_description' => "Professional {$service->name} services throughout {$location->name}.",
                'h1' => "{$service->name} in {$location->name}",
                'body_sections' => [],
            ];
        }

        return $content;
    }

    /**
     * Generate slug from service and location
     */
    protected function generateSlug(Service $service, City|County|State $location): string
    {
        $serviceName = Str::slug($service->name);
        
        if ($location instanceof City) {
            $locationName = Str::slug($location->name);
            $stateCode = strtolower($location->state->code);
            return "{$serviceName}-{$locationName}-{$stateCode}";
        }
        
        if ($location instanceof County) {
            $countyName = Str::slug($location->name);
            $stateCode = strtolower($location->state->code);
            return "{$countyName}-{$stateCode}-service-area";
        }
        
        $stateName = Str::slug($location->name);
        return "{$serviceName}-{$stateName}";
    }

    /**
     * Generate canonical URL
     */
    protected function generateCanonicalUrl(Site $site, string $slug): string
    {
        $domain = rtrim($site->domain, '/');
        return "https://{$domain}/{$slug}";
    }

    /**
     * Render body content from sections array
     */
    protected function renderBodyContent(array $sections): string
    {
        $html = '';
        
        foreach ($sections as $section) {
            $type = $section['type'] ?? 'default';
            $heading = $section['heading'] ?? null;
            $content = $section['content'] ?? null;
            
            if ($heading) {
                $html .= "<h2>{$heading}</h2>\n\n";
            }
            
            if ($content) {
                $html .= "<p>{$content}</p>\n\n";
            }
        }
        
        return trim($html);
    }

    /**
     * Generate excerpt from body sections
     */
    protected function generateExcerpt(array $sections, int $length = 200): string
    {
        foreach ($sections as $section) {
            if (isset($section['content']) && !empty($section['content'])) {
                $content = strip_tags($section['content']);
                
                if (strlen($content) <= $length) {
                    return $content;
                }
                
                return substr($content, 0, $length) . '...';
            }
        }
        
        return '';
    }

    /**
     * Generate schema.org JSON-LD
     */
    protected function generateSchema(Service $service, City|County|State $location, array $content): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $content['title'],
            'description' => $content['meta_description'],
        ];
        
        if ($location instanceof City) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'addressLocality' => $location->name,
                'addressRegion' => $location->state->code,
                'addressCountry' => 'US',
            ];
            
            if ($location->latitude && $location->longitude) {
                $schema['geo'] = [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                ];
            }
        }
        
        $schema['areaServed'] = [
            '@type' => 'City',
            'name' => $location->name,
        ];
        
        return $schema;
    }

    /**
     * Generate Open Graph tags
     */
    protected function generateOgTags(array $content, Site $site): array
    {
        return [
            'og:title' => $content['title'],
            'og:description' => $content['meta_description'],
            'og:type' => 'website',
            'og:site_name' => $site->name,
        ];
    }

    /**
     * Plan internal links for this payload
     */
    protected function planInternalLinks(Site $site, Service $service, City|County|State $location): array
    {
        $suggestions = [
            'links' => [],
            'anchors' => [],
            'parent_slug' => null,
            'hub_slug' => null,
            'related' => [],
        ];
        
        if ($location instanceof City) {
            // Parent: county hub
            $countySlug = Str::slug($location->county->name) . '-' . strtolower($location->state->code) . '-service-area';
            $suggestions['parent_slug'] = $countySlug;
            $suggestions['hub_slug'] = $countySlug;
            
            // Nearby cities with same service (would need database query - placeholder)
            $suggestions['links'][] = [
                'type' => 'parent',
                'slug' => $countySlug,
                'anchor' => "{$location->county->name} Service Area",
                'rel' => 'parent-page',
            ];
            
            // Anchor text variations
            $suggestions['anchors'] = [
                "{$service->name} in {$location->name}",
                "Professional {$service->name} {$location->name}",
                "{$location->name} {$service->name} Services",
            ];
        }
        
        return $suggestions;
    }

    /**
     * Generate submenu suggestions
     */
    protected function generateSubmenuSuggestions(Service $service, City|County|State $location): array
    {
        $suggestions = [];
        
        if ($location instanceof City) {
            $suggestions[] = [
                'label' => 'All Services',
                'type' => 'service_list',
            ];
            
            $suggestions[] = [
                'label' => 'Nearby Cities',
                'type' => 'location_list',
            ];
        }
        
        return $suggestions;
    }

    /**
     * Calculate sitemap priority based on opportunity score
     */
    protected function calculateSitemapPriority(SeoOpportunity $opportunity): float
    {
        // Higher priority score = higher sitemap priority
        $score = $opportunity->priority_score ?? 50;
        
        // Map 0-100 score to 0.3-1.0 sitemap priority
        return round(0.3 + ($score / 100) * 0.7, 2);
    }

    /**
     * Assess content quality score
     */
    protected function assessContentQuality(array $content): float
    {
        $score = 0;
        
        // Title quality (20 points)
        if (!empty($content['title']) && strlen($content['title']) >= 30) {
            $score += 20;
        }
        
        // Meta description quality (20 points)
        if (!empty($content['meta_description']) && strlen($content['meta_description']) >= 100) {
            $score += 20;
        }
        
        // Body sections count (40 points)
        $sectionCount = count($content['body_sections'] ?? []);
        $score += min(40, $sectionCount * 5);
        
        // Section content depth (20 points)
        $totalContentLength = 0;
        foreach ($content['body_sections'] ?? [] as $section) {
            $totalContentLength += strlen($section['content'] ?? '');
        }
        if ($totalContentLength >= 1000) {
            $score += 20;
        } elseif ($totalContentLength >= 500) {
            $score += 10;
        }
        
        return round($score, 2);
    }

    /**
     * Calculate SEO score
     */
    protected function calculateSeoScore(array $content, array $schema, array $linkSuggestions): float
    {
        $score = 0;
        
        // Schema presence (30 points)
        if (!empty($schema)) {
            $score += 30;
        }
        
        // Internal linking (30 points)
        $linkCount = count($linkSuggestions['links'] ?? []);
        $score += min(30, $linkCount * 10);
        
        // Hierarchy (20 points)
        if (!empty($linkSuggestions['parent_slug'])) {
            $score += 20;
        }
        
        // Content optimization (20 points)
        if (strlen($content['title'] ?? '') <= 60) {
            $score += 10; // Good title length for SEO
        }
        if (strlen($content['meta_description'] ?? '') >= 120 && strlen($content['meta_description'] ?? '') <= 160) {
            $score += 10; // Optimal meta description length
        }
        
        return round($score, 2);
    }

    /**
     * Batch generate payloads from multiple opportunities
     * 
     * @param array $opportunities
     * @param PageGenerationBatch|null $batch
     * @return array
     */
    public function batchGenerate(array $opportunities, ?PageGenerationBatch $batch = null): array
    {
        $results = [
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
            'errors' => [],
        ];
        
        foreach ($opportunities as $opportunity) {
            try {
                $payload = $this->generateFromOpportunity($opportunity, $batch);
                
                if ($payload->wasRecentlyCreated) {
                    $results['created']++;
                } else {
                    $results['updated']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'opportunity_id' => $opportunity->id,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    }
}
