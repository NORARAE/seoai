<?php

namespace App\Services;

use App\Models\SeoMarketingPage;

class PageImportService
{
    /** Cluster slug → JSON file map */
    private const CLUSTER_FILES = [
        'core'     => 'pages-core.json',
        'agency'   => 'pages-agency.json',
        'local'    => 'pages-local.json',
        'strategy' => 'pages-strategy.json',
        'industry' => 'pages-industry.json',
    ];

    /** @return array{imported:int, updated:int, skipped:int, errors:array} */
    public function import(): array
    {
        $stats  = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
        $index  = $this->loadIndex();          // slug => index meta
        $base   = base_path('docs/seo-architecture');

        foreach (self::CLUSTER_FILES as $cluster => $filename) {
            $path = $base . DIRECTORY_SEPARATOR . $filename;

            if (! file_exists($path)) {
                $stats['errors'][] = "Missing file: {$filename}";
                continue;
            }

            $pages = json_decode(file_get_contents($path), true);

            if (! is_array($pages)) {
                $stats['errors'][] = "Invalid JSON in: {$filename}";
                continue;
            }

            foreach ($pages as $page) {
                try {
                    $result = $this->importPage($page, $cluster, $index);
                    $stats[$result]++;
                } catch (\Throwable $e) {
                    $slug = $page['slug'] ?? 'unknown';
                    $stats['errors'][] = "Error on {$slug}: " . $e->getMessage();
                    $stats['skipped']++;
                }
            }
        }

        return $stats;
    }

    /** Parse one page array and upsert into seo_marketing_pages */
    private function importPage(array $p, string $cluster, array $index): string
    {
        $slug = ltrim($p['slug'] ?? ($p['url_path'] ?? ''), '/');

        if (empty($slug)) {
            throw new \RuntimeException('Page has no slug and no url_path');
        }

        $content = $p['content'] ?? [];
        $meta    = $p['meta']    ?? [];
        $sitemap = $p['sitemap'] ?? [];
        $links   = $p['internal_links'] ?? null;

        // Prefer index.json for priority rank (it has the most consistent data)
        $indexMeta      = $index[$slug] ?? [];
        $moneyPageRank  = $p['money_page_rank'] ?? ($indexMeta['money_page_rank'] ?? null);
        $sitemapPriority = $sitemap['priority']   ?? ($indexMeta['sitemap_priority'] ?? 0.5);
        $sitemapFile    = $sitemap['sitemap_file'] ?? ($indexMeta['sitemap_file'] ?? null);

        // Determine priority_level string from sitemap priority
        $priorityLevel = match (true) {
            $sitemapPriority >= 1.0  => 'tier_1',
            $sitemapPriority >= 0.9  => 'tier_1',
            $sitemapPriority >= 0.8  => 'tier_2',
            $sitemapPriority >= 0.7  => 'tier_2',
            default                  => 'tier_3',
        };

        $data = [
            'cluster'               => $p['cluster']       ?? $cluster,
            'search_intent'         => $p['search_intent'] ?? null,
            'nav_label'             => $p['nav_label']     ?? null,
            'primary_keyword'       => $p['keywords']['primary']    ?? null,
            'secondary_keywords'    => $p['keywords']['secondary']  ?? null,

            // Meta
            'meta_title'            => $meta['title']          ?? null,
            'meta_description'      => $meta['description']    ?? null,
            'og_title'              => $meta['og_title']        ?? null,
            'og_description'        => $meta['og_description']  ?? null,

            // Content
            'h1'                    => $content['h1']                     ?? null,
            'h2_structure'          => $content['h2s']                    ?? null,
            'hook'                  => $content['hook']                   ?? null,
            'system_explanation'    => $content['system_explanation']     ?? null,
            'benefits'              => $content['benefits']               ?? null,
            'exclusivity'           => $content['exclusivity']            ?? null,
            'use_cases'             => $content['use_cases']              ?? null,
            'internal_linking_section' => $content['internal_linking_section'] ?? null,

            // CTAs
            'cta_top'               => $content['cta_top']    ?? null,
            'cta_mid'               => $content['cta_mid']    ?? null,
            'cta_bottom'            => $content['cta_bottom'] ?? null,

            // Links
            'internal_links'        => $links,

            // Sitemap
            'sitemap_priority'      => $sitemapPriority,
            'sitemap_changefreq'    => $sitemap['changefreq'] ?? 'monthly',
            'sitemap_file'          => $sitemapFile,

            // Ranking
            'money_page_rank'       => $moneyPageRank ? (int) $moneyPageRank : null,
            'priority_level'        => $priorityLevel,
            'is_indexed'            => true,

            // Schema left null — model builds baseline dynamically
            'schema_json'           => null,
        ];

        $existing = SeoMarketingPage::where('url_slug', $slug)->first();

        SeoMarketingPage::updateOrCreate(
            ['url_slug' => $slug],
            $data
        );

        return $existing ? 'updated' : 'imported';
    }

    /**
     * Load index.json and return a map keyed by slug (without leading slash)
     * @return array<string, array>
     */
    private function loadIndex(): array
    {
        $path = base_path('docs/seo-architecture/index.json');

        if (! file_exists($path)) {
            return [];
        }

        $raw = json_decode(file_get_contents($path), true);

        if (empty($raw['page_index']) || ! is_array($raw['page_index'])) {
            return [];
        }

        $map = [];
        foreach ($raw['page_index'] as $entry) {
            $slug = ltrim($entry['url_path'] ?? '', '/');
            if ($slug !== '') {
                $map[$slug] = $entry;
            }
        }

        return $map;
    }
}
