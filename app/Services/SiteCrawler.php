<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class SiteCrawler
{
    /**
     * Timeout for HTTP requests in seconds
     */
    protected int $timeout = 30;

    /**
     * User agent string for requests
     */
    protected string $userAgent = 'SEOAIco/1.0 (SEO Crawler)';

    /**
     * Crawl a site's homepage and extract all valid links
     *
     * @param string $domain Domain without protocol (e.g., 'bionw.com')
     * @return array{
     *     success: bool,
     *     url: string,
     *     links: array<string>,
     *     error: string|null,
     *     status_code: int|null
     * }
     */
    public function crawlHomepage(string $domain): array
    {
        // Normalize the domain and build URL
        $url = $this->normalizeUrl($domain);

        try {
            // Make HTTP request
            $response = Http::timeout($this->timeout)
                ->withUserAgent($this->userAgent)
                ->get($url);

            // Check if request was successful
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'url' => $url,
                    'links' => [],
                    'error' => "HTTP {$response->status()}: Failed to fetch page",
                    'status_code' => $response->status(),
                ];
            }

            // Get HTML content
            $html = $response->body();

            // Parse HTML and extract links
            $links = $this->extractLinks($html, $url);

            return [
                'success' => true,
                'url' => $url,
                'links' => $links,
                'error' => null,
                'status_code' => $response->status(),
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Connection error crawling {$url}: {$e->getMessage()}");

            return [
                'success' => false,
                'url' => $url,
                'links' => [],
                'error' => "Connection error: {$e->getMessage()}",
                'status_code' => null,
            ];
        } catch (\Exception $e) {
            Log::error("Error crawling {$url}: {$e->getMessage()}");

            return [
                'success' => false,
                'url' => $url,
                'links' => [],
                'error' => "Error: {$e->getMessage()}",
                'status_code' => null,
            ];
        }
    }

    /**
     * Normalize domain to a full HTTPS URL
     */
    protected function normalizeUrl(string $domain): string
    {
        // Remove any existing protocol
        $domain = preg_replace('#^https?://#i', '', $domain);

        // Remove trailing slash
        $domain = rtrim($domain, '/');

        // Add https:// prefix
        return 'https://' . $domain;
    }

    /**
     * Extract and filter links from HTML
     *
     * @param string $html HTML content
     * @param string $baseUrl Base URL for resolving relative links
     * @return array<string> Array of normalized URLs
     */
    protected function extractLinks(string $html, string $baseUrl): array
    {
        $crawler = new Crawler($html, $baseUrl);
        $links = [];

        // Extract all anchor tags with href attributes
        $crawler->filter('a[href]')->each(function (Crawler $node) use (&$links, $baseUrl) {
            $href = $node->attr('href');

            if ($this->isValidLink($href)) {
                $normalizedLink = $this->resolveUrl($href, $baseUrl);

                if ($normalizedLink && $this->isInternalLink($normalizedLink, $baseUrl)) {
                    $links[] = $normalizedLink;
                }
            }
        });

        // Remove duplicates and return
        return array_values(array_unique($links));
    }

    /**
     * Check if a link is valid for crawling
     */
    protected function isValidLink(string $href): bool
    {
        // Trim whitespace
        $href = trim($href);

        // Ignore empty links
        if (empty($href)) {
            return false;
        }

        // Ignore javascript:, mailto:, tel:, etc.
        if (preg_match('#^(javascript|mailto|tel|ftp|data):#i', $href)) {
            return false;
        }

        // Ignore fragments only (e.g., "#section")
        if (str_starts_with($href, '#')) {
            return false;
        }

        return true;
    }

    /**
     * Resolve relative URLs to absolute URLs
     */
    protected function resolveUrl(string $href, string $baseUrl): ?string
    {
        try {
            // Parse base URL
            $baseParts = parse_url($baseUrl);

            if (!$baseParts || !isset($baseParts['host'])) {
                return null;
            }

            // If href is already absolute, return as-is
            if (parse_url($href, PHP_URL_SCHEME)) {
                return $this->cleanUrl($href);
            }

            // Handle protocol-relative URLs (//example.com/path)
            if (str_starts_with($href, '//')) {
                return $this->cleanUrl(($baseParts['scheme'] ?? 'https') . ':' . $href);
            }

            // Handle absolute paths (/path/to/page)
            if (str_starts_with($href, '/')) {
                $scheme = $baseParts['scheme'] ?? 'https';
                $host = $baseParts['host'];
                $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';

                return $this->cleanUrl($scheme . '://' . $host . $port . $href);
            }

            // Handle relative paths (page.html, ./page.html)
            $basePath = $baseParts['path'] ?? '/';
            $basePath = rtrim(dirname($basePath), '/');

            $scheme = $baseParts['scheme'] ?? 'https';
            $host = $baseParts['host'];
            $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';

            return $this->cleanUrl($scheme . '://' . $host . $port . $basePath . '/' . ltrim($href, './'));
        } catch (\Exception $e) {
            Log::warning("Failed to resolve URL: {$href} with base {$baseUrl}");
            return null;
        }
    }

    /**
     * Clean URL by removing fragments and normalizing
     */
    protected function cleanUrl(string $url): string
    {
        // Remove fragment (#section)
        $url = preg_replace('/#.*$/', '', $url);

        // Remove trailing slash for consistency (except for domain root)
        $parts = parse_url($url);
        if (isset($parts['path']) && $parts['path'] !== '/') {
            $url = rtrim($url, '/');
        }

        return $url;
    }

    /**
     * Check if a URL is internal (same domain) or external
     */
    protected function isInternalLink(string $url, string $baseUrl): bool
    {
        $urlHost = parse_url($url, PHP_URL_HOST);
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);

        // Normalize hosts (remove www.)
        $urlHost = preg_replace('/^www\./i', '', $urlHost ?? '');
        $baseHost = preg_replace('/^www\./i', '', $baseHost ?? '');

        return $urlHost === $baseHost;
    }

    /**
     * Set custom timeout
     */
    public function setTimeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * Set custom user agent
     */
    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }
}
