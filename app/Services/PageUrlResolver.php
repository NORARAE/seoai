<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Page;
use App\Models\LocationPage;
use Illuminate\Support\Facades\Log;

class PageUrlResolver
{
    /**
     * Resolve a GSC URL to existing Page or LocationPage records
     * 
     * Returns: ['page_id' => int|null, 'location_page_id' => int|null]
     */
    public function resolve(Site $site, string $url): array
    {
        $result = [
            'page_id' => null,
            'location_page_id' => null,
        ];

        // Normalize URL for comparison
        $normalizedUrl = $this->normalizeUrl($url);
        $path = parse_url($normalizedUrl, PHP_URL_PATH) ?? '/';

        // Try to match LocationPage by url_path first (most specific)
        $locationPage = LocationPage::where('site_id', $site->id)
            ->where(function($query) use ($path, $normalizedUrl) {
                $query->where('url_path', $path)
                    ->orWhere('canonical_url', $normalizedUrl);
            })
            ->first();

        if ($locationPage) {
            $result['location_page_id'] = $locationPage->id;
            return $result;
        }

        // Try to match regular Page by URL or path
        $page = Page::where('site_id', $site->id)
            ->where(function($query) use ($path, $normalizedUrl, $url) {
                $query->where('url', $url)
                    ->orWhere('url', $normalizedUrl)
                    ->orWhere('path', $path);
            })
            ->first();

        if ($page) {
            $result['page_id'] = $page->id;
            return $result;
        }

        // No match found - this is OK, we store the URL anyway
        // Future: Could auto-create Page records for unmatched URLs
        
        return $result;
    }

    /**
     * Batch resolve multiple URLs (for efficiency)
     */
    public function batchResolve(Site $site, array $urls): array
    {
        $results = [];
        
        foreach ($urls as $url) {
            $results[$url] = $this->resolve($site, $url);
        }
        
        return $results;
    }

    /**
     * Normalize URL for consistent matching
     */
    protected function normalizeUrl(string $url): string
    {
        // Remove trailing slash
        $url = rtrim($url, '/');
        
        // Remove fragment
        $url = explode('#', $url)[0];
        
        // Remove common tracking parameters
        $parsed = parse_url($url);
        
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $params);
            
            // Remove common tracking params
            $trackingParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'fbclid', 'gclid'];
            foreach ($trackingParams as $param) {
                unset($params[$param]);
            }
            
            if (!empty($params)) {
                $url = $parsed['scheme'] . '://' . $parsed['host'] . ($parsed['path'] ?? '') . '?' . http_build_query($params);
            } else {
                $url = $parsed['scheme'] . '://' . $parsed['host'] . ($parsed['path'] ?? '');
            }
        }
        
        return $url;
    }

    /**
     * Get unresolved URLs for a site (URLs in GSC but not in our DB)
     */
    public function getUnresolvedUrls(Site $site, int $minImpressions = 100): array
    {
        return \DB::table('performance_metrics')
            ->where('site_id', $site->id)
            ->whereNull('page_id')
            ->whereNull('location_page_id')
            ->select('url')
            ->selectRaw('SUM(impressions) as total_impressions')
            ->groupBy('url')
            ->having('total_impressions', '>=', $minImpressions)
            ->orderByDesc('total_impressions')
            ->get()
            ->pluck('url')
            ->toArray();
    }
}
