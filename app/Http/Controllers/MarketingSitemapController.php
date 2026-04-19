<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class MarketingSitemapController extends Controller
{
    private const VALID_CLUSTERS = ['core', 'agency', 'local', 'strategy', 'industry'];

    private const CLUSTER_PAGES = [
        'core' => [
            ['path' => '/', 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['path' => '/pricing', 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['path' => '/how-it-works', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/solutions', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/solutions/agencies', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/solutions/business-owners', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/about', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ],
        'agency' => [
            ['path' => '/growth-services', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/web-design-development', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/wordpress-support', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/ads-management', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/branding-print', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/access-plans', 'changefreq' => 'monthly', 'priority' => '0.7'],
        ],
        'local' => [
            ['path' => '/local-ai-search', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/ai-seo-for-local-businesses', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/how-ai-search-works', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/how-ai-retrieves-content', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/how-chatgpt-chooses-sources', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/optimize-for-ai-answers', 'changefreq' => 'monthly', 'priority' => '0.7'],
        ],
        'strategy' => [
            ['path' => '/what-is-ai-search-optimization', 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['path' => '/ai-search-optimization', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/ai-search-optimization-guide', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/ai-citation-engine', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/search-presence-engine', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/generative-engine-optimization', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/entity-seo-for-ai-search', 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['path' => '/aeo-vs-seo-vs-geo', 'changefreq' => 'monthly', 'priority' => '0.8'],
        ],
        'industry' => [
            ['path' => '/ai-seo-for-chatgpt-geo-aeo', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/programmatic-seo-platform', 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['path' => '/chatgpt-seo', 'changefreq' => 'monthly', 'priority' => '0.7'],
        ],
    ];

    /** Sitemap index listing only populated cluster sitemaps */
    public function index(): Response
    {
        $sitemaps = collect(self::VALID_CLUSTERS)
            ->map(fn(string $cluster) => [
                'cluster' => $cluster,
                'entries' => $this->clusterEntries($cluster),
            ])
            ->filter(fn(array $clusterData) => !empty($clusterData['entries']))
            ->map(fn(array $clusterData) => [
                'loc' => url('/sitemaps/marketing-' . $clusterData['cluster'] . '.xml'),
                'lastmod' => now()->toDateString(),
            ]);

        $xml = $this->buildIndex($sitemaps->all());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /** Individual cluster sitemap */
    public function cluster(string $cluster): Response
    {
        abort_unless(in_array($cluster, self::VALID_CLUSTERS, true), 404);

        $entries = $this->clusterEntries($cluster);

        $xml = $this->buildUrlset($entries);

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    private function clusterEntries(string $cluster): array
    {
        return collect(self::CLUSTER_PAGES[$cluster] ?? [])
            ->filter(fn(array $page) => !empty($page['path']))
            ->map(fn(array $page) => [
                'loc' => url($page['path']),
                'lastmod' => now()->toDateString(),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ])
            ->values()
            ->all();
    }

    // ── XML builders (no Blade — pure string for performance) ────────────────

    private function buildIndex(array $sitemaps): string
    {
        $items = '';
        foreach ($sitemaps as $s) {
            $items .= "\n  <sitemap>\n"
                . "    <loc>" . e($s['loc']) . "</loc>\n"
                . "    <lastmod>" . e($s['lastmod']) . "</lastmod>\n"
                . "  </sitemap>";
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . "\n<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">"
            . $items
            . "\n</sitemapindex>\n";
    }

    private function buildUrlset(array $entries): string
    {
        $items = '';
        foreach ($entries as $e) {
            $items .= "\n  <url>\n"
                . "    <loc>" . e($e['loc']) . "</loc>\n"
                . "    <lastmod>" . e($e['lastmod']) . "</lastmod>\n"
                . "    <changefreq>" . e($e['changefreq']) . "</changefreq>\n"
                . "    <priority>" . e($e['priority']) . "</priority>\n"
                . "  </url>";
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">"
            . $items
            . "\n</urlset>\n";
    }
}
