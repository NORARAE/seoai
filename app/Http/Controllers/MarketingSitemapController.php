<?php

namespace App\Http\Controllers;

use App\Models\SeoMarketingPage;
use Illuminate\Http\Response;

class MarketingSitemapController extends Controller
{
    private const VALID_CLUSTERS = ['core', 'agency', 'local', 'strategy', 'industry'];

    /** Sitemap index listing all 5 cluster sitemaps */
    public function index(): Response
    {
        $sitemaps = collect(self::VALID_CLUSTERS)->map(fn($c) => [
            'loc' => url("/sitemaps/marketing-{$c}.xml"),
            'lastmod' => now()->toDateString(),
        ]);

        $xml = $this->buildIndex($sitemaps->all());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /** Individual cluster sitemap */
    public function cluster(string $cluster): Response
    {
        abort_unless(in_array($cluster, self::VALID_CLUSTERS, true), 404);

        $pages = SeoMarketingPage::where('cluster', $cluster)
            ->where('is_indexed', true)
            ->orderByRaw('COALESCE(money_page_rank, 999)')
            ->get(['url_slug', 'sitemap_priority', 'sitemap_changefreq', 'updated_at']);

        // Always include homepage first in the core cluster sitemap
        $entries = [];
        if ($cluster === 'core') {
            $entries[] = [
                'loc' => url('/'),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ];
            // Static core pages — always included regardless of DB state
            foreach ([
                ['path' => '/book', 'changefreq' => 'weekly', 'priority' => '0.9'],
                ['path' => '/how-it-works', 'changefreq' => 'monthly', 'priority' => '0.8'],
                ['path' => '/solutions', 'changefreq' => 'monthly', 'priority' => '0.8'],
                ['path' => '/access-plans', 'changefreq' => 'monthly', 'priority' => '0.8'],
                ['path' => '/growth-services', 'changefreq' => 'monthly', 'priority' => '0.7'],
                ['path' => '/web-design-development', 'changefreq' => 'monthly', 'priority' => '0.7'],
                ['path' => '/wordpress-support', 'changefreq' => 'monthly', 'priority' => '0.7'],
                ['path' => '/ads-management', 'changefreq' => 'monthly', 'priority' => '0.7'],
                ['path' => '/branding-print', 'changefreq' => 'monthly', 'priority' => '0.7'],
                ['path' => '/rd-tax-credit', 'changefreq' => 'monthly', 'priority' => '0.6'],
                ['path' => '/onboarding/start', 'changefreq' => 'monthly', 'priority' => '0.5'],
            ] as $static) {
                $entries[] = [
                    'loc' => url($static['path']),
                    'lastmod' => now()->toDateString(),
                    'changefreq' => $static['changefreq'],
                    'priority' => $static['priority'],
                ];
            }
        }

        foreach ($pages as $page) {
            $entries[] = [
                'loc' => url('/' . $page->url_slug),
                'lastmod' => $page->updated_at?->toDateString() ?? now()->toDateString(),
                'changefreq' => $page->sitemap_changefreq ?? 'monthly',
                'priority' => number_format((float) ($page->sitemap_priority ?? 0.7), 1),
            ];
        }

        $xml = $this->buildUrlset($entries);

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
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
