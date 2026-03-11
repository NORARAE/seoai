<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Str;

class TitleSuggestionService
{
    /**
     * Generate an SEO-friendly title suggestion for a page
     */
    public function generateSuggestion(Page $page): string
    {
        // Get the site name for branding
        $siteName = $page->site->name ?? $page->site->domain;
        
        // Parse the URL path
        $path = trim($page->path, '/');
        
        // Home page
        if (empty($path)) {
            return $this->cleanBrandName($siteName) . ' - Home';
        }
        
        // Split path into segments
        $segments = array_filter(explode('/', $path));
        
        // Get the last segment (usually the page slug)
        $lastSegment = end($segments);
        
        // Remove common file extensions
        $slug = preg_replace('/\.(html|php|htm|asp|aspx)$/i', '', $lastSegment);
        
        // Convert slug to readable title
        $title = $this->slugToTitle($slug);
        
        // Add context from parent segments if available
        if (count($segments) > 1) {
            $parentSegment = $segments[count($segments) - 2];
            $context = $this->slugToTitle($parentSegment);
            
            // Avoid redundancy
            if (!Str::contains(strtolower($title), strtolower($context))) {
                $title = $title . ' - ' . $context;
            }
        }
        
        // Add site branding
        $brandName = $this->cleanBrandName($siteName);
        if (!Str::contains(strtolower($title), strtolower($brandName))) {
            $title .= ' | ' . $brandName;
        }
        
        // Ensure reasonable length (60 chars is SEO sweet spot)
        if (strlen($title) > 60) {
            // Try without middle context
            $simpleTitle = $this->slugToTitle($slug) . ' | ' . $brandName;
            if (strlen($simpleTitle) <= 60) {
                $title = $simpleTitle;
            } else {
                // Just use the slug title
                $title = $this->slugToTitle($slug);
            }
        }
        
        return $title;
    }
    
    /**
     * Convert a URL slug to a readable title
     */
    private function slugToTitle(string $slug): string
    {
        // Remove common separators and convert to title case
        $title = str_replace(['-', '_', '+'], ' ', $slug);
        
        // Handle camelCase
        $title = preg_replace('/([a-z])([A-Z])/', '$1 $2', $title);
        
        // Clean up multiple spaces
        $title = preg_replace('/\s+/', ' ', $title);
        
        // Title case with proper handling of common words
        $title = Str::title($title);
        
        // Fix common acronyms
        $title = str_replace(['Seo ', 'Faq ', 'Api ', 'Url ', 'Html ', 'Css ', 'Js '], 
                           ['SEO ', 'FAQ ', 'API ', 'URL ', 'HTML ', 'CSS ', 'JS '], 
                           $title);
        
        return trim($title);
    }
    
    /**
     * Clean up brand name for consistent display
     */
    private function cleanBrandName(string $name): string
    {
        // Remove common TLDs from domain names
        $name = preg_replace('/\.(com|org|net|edu|gov|io|co|uk)$/i', '', $name);
        
        // Title case
        $name = Str::title($name);
        
        return trim($name);
    }
}
