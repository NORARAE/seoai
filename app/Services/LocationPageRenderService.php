<?php

namespace App\Services;

use App\Models\LocationPage;

/**
 * LocationPageRenderService
 * 
 * Renders LocationPage records into clean, reusable HTML
 * with AMBIo-inspired section structure and CSS classes.
 * 
 * Used by:
 * - Preview rendering
 * - Export command (rendered_html field)
 * - Future WordPress sync
 */
class LocationPageRenderService
{
    protected LocationSchemaBuilder $schemaBuilder;

    public function __construct(LocationSchemaBuilder $schemaBuilder)
    {
        $this->schemaBuilder = $schemaBuilder;
    }

    /**
     * Render a complete LocationPage to HTML with optional schema
     *
     * @param LocationPage $page
     * @param array $options
     * @return string
     */
    public function render(LocationPage $page, array $options = []): string
    {
        $options = array_merge([
            'include_h1' => true,
            'include_internal_links' => true,
            'include_schema' => false,
            'wrapper_class' => 'abm-location-page',
        ], $options);

        $html = '';

        // Schema tags (if requested)
        if ($options['include_schema']) {
            $html .= $this->schemaBuilder->renderAllSchemaTags($page);
        }

        // Wrapper
        if ($options['wrapper_class']) {
            $html .= '<div class="' . $options['wrapper_class'] . '">' . "\n";
        }

        // H1
        if ($options['include_h1'] && $page->h1) {
            $html .= $this->renderH1($page->h1);
        }

        // Body sections
        if ($page->body_sections_json && is_array($page->body_sections_json)) {
            foreach ($page->body_sections_json as $section) {
                if (isset($section['type']) && $section['type'] !== 'internal_links') {
                    $html .= $this->renderSection($section);
                }
            }
        }

        // Internal links
        if ($options['include_internal_links'] && $page->internal_links_json) {
            $html .= $this->renderInternalLinks($page->internal_links_json);
        }

        // Close wrapper
        if ($options['wrapper_class']) {
            $html .= '</div>' . "\n";
        }

        return $html;
    }

    /**
     * Render and cache the page HTML
     *
     * Updates the rendered_html_cache, rendered_excerpt_cache, and schema fields
     *
     * @param LocationPage $page
     * @param string $version
     * @return void
     */
    public function renderAndCache(LocationPage $page, string $version = '1.0'): void
    {
        // Generate schemas
        $schemas = $this->schemaBuilder->generateAllSchemas($page);
        
        // Render HTML (without schema tags - those are cached separately)
        $html = $this->render($page, ['include_schema' => false]);
        
        // Generate excerpt
        $excerpt = $this->getExcerpt($page);
        
        // Update page cache fields
        $page->update([
            'render_version' => $version,
            'rendered_html_cache' => $html,
            'rendered_excerpt_cache' => $excerpt,
            'faq_schema_json' => $schemas['faq_schema'],
            'service_schema_json' => $schemas['service_schema'],
            'local_business_schema_json' => $schemas['local_business_schema'],
            'schema_cache_json' => $schemas,
            'rendered_at' => now(),
            'needs_render' => false,
        ]);
    }

    /**
     * Render H1 heading
     *
     * @param string $h1
     * @return string
     */
    protected function renderH1(string $h1): string
    {
        return '<h1 class="abm-page-title">' . htmlspecialchars($h1) . '</h1>' . "\n\n";
    }

    /**
     * Render a body section based on type
     *
     * @param array $section
     * @return string
     */
    protected function renderSection(array $section): string
    {
        $type = $section['type'] ?? 'default';
        $heading = $section['heading'] ?? null;
        $content = $section['content'] ?? null;

        // Route to specific section renderer
        return match($type) {
            'hero' => $this->renderHeroSection($heading, $content),
            'intro' => $this->renderIntroSection($heading, $content),
            'service_overview', 'service_description' => $this->renderServiceSection($heading, $content),
            'local_relevance', 'county_support' => $this->renderLocalRelevanceSection($heading, $content),
            'coverage_area' => $this->renderCoverageSection($heading, $content),
            'why_choose' => $this->renderWhyChooseSection($heading, $content),
            'cta' => $this->renderCtaSection($heading, $content),
            default => $this->renderGenericSection($type, $heading, $content),
        };
    }

    /**
     * Render hero section (AMBIo-inspired)
     *
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderHeroSection(?string $heading, ?string $content): string
    {
        $html = '<section class="abm-hero abm-section">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-hero__title">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-hero__content">' . "\n";
            $html .= '    <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render intro section
     *
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderIntroSection(?string $heading, ?string $content): string
    {
        $html = '<section class="abm-intro abm-section">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-section__heading">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-section__content">' . "\n";
            $html .= '    <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render service overview/description section
     *
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderServiceSection(?string $heading, ?string $content): string
    {
        $html = '<section class="abm-service-overview abm-section">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-section__heading">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-section__content">' . "\n";
            $html .= '    <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render local relevance section
     *
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderLocalRelevanceSection(?string $heading, ?string $content): string
    {
        $html = '<section class="abm-local-relevance abm-section">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-section__heading">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-section__content abm-local-focus">' . "\n";
            $html .= '    <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render coverage area section
     *
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderCoverageSection(?string $heading, ?string $content): string
    {
        $html = '<section class="abm-coverage abm-section">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-section__heading">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-section__content">' . "\n";
            $html .= '    <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render "Why Choose Us" section (AMBIo-inspired card grid)
     *
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderWhyChooseSection(?string $heading, ?string $content): string
    {
        $html = '<section class="abm-why-choose abm-section">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-section__heading">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-card-grid">' . "\n";
            $html .= '    <div class="abm-card">' . "\n";
            $html .= '      <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '    </div>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render CTA section (AMBIo-inspired with strong action language)
     *
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderCtaSection(?string $heading, ?string $content): string
    {
        $html = '<section class="abm-cta abm-section">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-cta__heading">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-cta__content">' . "\n";
            $html .= '    <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render generic section
     *
     * @param string $type
     * @param string|null $heading
     * @param string|null $content
     * @return string
     */
    protected function renderGenericSection(string $type, ?string $heading, ?string $content): string
    {
        $safeType = preg_replace('/[^a-z0-9\-_]/', '', strtolower($type));
        
        $html = '<section class="abm-section abm-section--' . $safeType . '">' . "\n";
        
        if ($heading) {
            $html .= '  <h2 class="abm-section__heading">' . htmlspecialchars($heading) . '</h2>' . "\n";
        }
        
        if ($content) {
            $html .= '  <div class="abm-section__content">' . "\n";
            $html .= '    <p>' . htmlspecialchars($content) . '</p>' . "\n";
            $html .= '  </div>' . "\n";
        }
        
        $html .= '</section>' . "\n\n";
        
        return $html;
    }

    /**
     * Render body sections from body_sections_json
     *
     * Skips 'internal_links' type sections as those are rendered separately
     *
     * @param array $sections
     * @return string
     */
    protected function renderBodySections(array $sections): string
    {
        $html = '';

        foreach ($sections as $section) {
            // Skip internal_links type - those are rendered separately
            if (isset($section['type']) && $section['type'] === 'internal_links') {
                continue;
            }

            $type = $section['type'] ?? 'default';
            $html .= "<section class=\"section section-{$type}\">\n";

            // Render heading if present
            if (isset($section['heading']) && !empty($section['heading'])) {
                $html .= "  <h2>" . htmlspecialchars($section['heading'], ENT_QUOTES, 'UTF-8') . "</h2>\n";
            }

            // Render content if present
            if (isset($section['content']) && !empty($section['content'])) {
                // Wrap content in paragraphs if it contains newlines
                $content = $section['content'];
                
                if (strpos($content, "\n") !== false) {
                    // Split by double newlines for paragraphs
                    $paragraphs = explode("\n\n", $content);
                    foreach ($paragraphs as $para) {
                        $para = trim($para);
                        if (!empty($para)) {
                            $html .= "  <p>" . nl2br(htmlspecialchars($para, ENT_QUOTES, 'UTF-8')) . "</p>\n";
                        }
                    }
                } else {
                    // Single paragraph
                    $html .= "  <p>" . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . "</p>\n";
                }
            }

            $html .= "</section>\n\n";
        }

        return $html;
    }

    /**
     * Render internal links from internal_links_json (AMBIo-inspired)
     *
     * @param array $linksData
     * @return string
     */
    protected function renderInternalLinks(?array $linksData): string
    {
        if (!$linksData || !isset($linksData['links']) || empty($linksData['links'])) {
            return '';
        }

        $html = '<section class="abm-internal-links abm-section">' . "\n";
        $html .= '  <h2 class="abm-section__heading">Related Service Areas</h2>' . "\n";
        $html .= '  <div class="abm-link-grid">' . "\n";

        foreach ($linksData['links'] as $link) {
            $url = $link['url'] ?? '#';
            $anchor = $link['anchor'] ?? 'View Page';
            $rel = $link['rel'] ?? '';
            $type = $link['type'] ?? '';

            $html .= '    <a href="' . htmlspecialchars($url) . '" class="abm-link-card">' . "\n";
            $html .= '      <span class="abm-link-card__title">' . htmlspecialchars($anchor) . '</span>' . "\n";

            // Add metadata badge
            if (isset($link['distance_miles'])) {
                $distance = number_format($link['distance_miles'], 1);
                $html .= '      <span class="abm-link-card__meta">📍 ' . $distance . ' miles away</span>' . "\n";
            } elseif ($rel === 'parent-page' || $type === 'county-hub') {
                $html .= '      <span class="abm-link-card__meta">🏠 County Hub</span>' . "\n";
            }

            $html .= '    </a>' . "\n";
        }

        $html .= '  </div>' . "\n";
        $html .= '</section>' . "\n\n";

        return $html;
    }

    /**
     * Render just the body content without H1
     *
     * Useful for embedding in other contexts
     *
     * @param LocationPage $page
     * @return string
     */
    public function renderBodyOnly(LocationPage $page): string
    {
        $html = '';

        // Render body sections
        if ($page->body_sections_json && is_array($page->body_sections_json)) {
            foreach ($page->body_sections_json as $section) {
                if (isset($section['type']) && $section['type'] !== 'internal_links') {
                    $html .= $this->renderSection($section);
                }
            }
        }

        // Render internal links
        if ($page->internal_links_json) {
            $html .= $this->renderInternalLinks($page->internal_links_json);
        }

        return $html;
    }

    /**
     * Render just the internal links (no wrapper)
     * Useful for partial rendering
     *
     * @param LocationPage $page
     * @return string
     */
    public function renderLinksOnly(LocationPage $page): string
    {
        return $this->renderInternalLinks($page->internal_links_json);
    }

    /**
     * Get a plain text excerpt from the page content
     *
     * @param LocationPage $page
     * @param int $length
     * @return string
     */
    public function getExcerpt(LocationPage $page, int $length = 200): string
    {
        if (!$page->body_sections_json || !is_array($page->body_sections_json)) {
            return '';
        }

        // Find the first section with content
        foreach ($page->body_sections_json as $section) {
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
}
