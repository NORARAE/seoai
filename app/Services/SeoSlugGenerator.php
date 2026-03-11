<?php

namespace App\Services;

class SeoSlugGenerator
{
    /**
     * Generate a clean SEO-friendly slug from input text
     *
     * Rules:
     * - lowercase only
     * - hyphenated
     * - & replaced with 'and'
     * - punctuation stripped
     * - duplicate separators collapsed
     *
     * @param string $text
     * @return string
     */
    public function generate(string $text): string
    {
        // Convert to lowercase
        $slug = strtolower($text);
        
        // Replace & with 'and'
        $slug = str_replace('&', 'and', $slug);
        
        // Replace apostrophes and quotes with nothing
        $slug = str_replace(["'", '"'], '', $slug);
        
        // Replace spaces and common separators with hyphens
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        
        // Remove all non-alphanumeric characters except hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        
        // Collapse multiple hyphens into one
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        return $slug;
    }

    /**
     * Generate county hub URL path
     *
     * Format: /{county-slug}-county-{state-code}/
     *
     * @param string $countyName
     * @param string $stateCode
     * @return string
     */
    public function generateCountyHubPath(string $countyName, string $stateCode): string
    {
        $countySlug = $this->generate($countyName);
        $stateCodeLower = strtolower($stateCode);
        
        return "/{$countySlug}-county-{$stateCodeLower}/";
    }

    /**
     * Generate service-city URL path
     *
     * Format: /{service-slug}-{city-slug}-{state-code}/
     *
     * @param string $serviceName
     * @param string $cityName
     * @param string $stateCode
     * @return string
     */
    public function generateServiceCityPath(string $serviceName, string $cityName, string $stateCode): string
    {
        $serviceSlug = $this->generate($serviceName);
        $citySlug = $this->generate($cityName);
        $stateCodeLower = strtolower($stateCode);
        
        return "/{$serviceSlug}-{$citySlug}-{$stateCodeLower}/";
    }

    /**
     * Generate a unique slug from a URL path
     *
     * @param string $urlPath
     * @return string
     */
    public function generateSlugFromPath(string $urlPath): string
    {
        // Remove leading and trailing slashes
        $slug = trim($urlPath, '/');
        
        // Replace remaining slashes with hyphens
        $slug = str_replace('/', '-', $slug);
        
        return $slug;
    }
}
