<?php

namespace App\Services\Crawl;

use App\Models\Site;
use Illuminate\Support\Facades\DB;

/**
 * Computes a structured summary of crawl data for a site.
 *
 * All queries target completed url_inventory rows so that partial crawls
 * still return usable data. Returns null only when zero crawl data exists.
 */
class CrawlSummaryService
{
    public function compute(Site $site): ?array
    {
        $siteId = $site->id;

        $totalDiscovered = DB::table('url_inventory')
            ->where('site_id', $siteId)
            ->count();

        if ($totalDiscovered === 0) {
            return null;
        }

        $totalCrawled = DB::table('url_inventory')
            ->where('site_id', $siteId)
            ->where('status', 'completed')
            ->count();

        // Indexable: completed AND not in a non-indexable state
        $indexable = DB::table('url_inventory')
            ->where('site_id', $siteId)
            ->where('status', 'completed')
            ->whereNotIn('indexability_status', ['blocked', 'non_200', 'noindex'])
            ->count();

        // Single-pass metadata stats via conditional aggregation
        $metaStats = DB::table('url_inventory as ui')
            ->join('page_metadata as pm', 'pm.url_id', '=', 'ui.id')
            ->where('ui.site_id', $siteId)
            ->where('ui.status', 'completed')
            ->selectRaw("
                COUNT(*) as pages_with_meta,
                SUM(CASE WHEN pm.title  IS NOT NULL AND pm.title  != '' THEN 1 ELSE 0 END) as pages_with_title,
                SUM(CASE WHEN pm.title  IS NULL     OR  pm.title  = ''  THEN 1 ELSE 0 END) as pages_missing_title,
                SUM(CASE WHEN pm.h1     IS NOT NULL AND pm.h1     != '' THEN 1 ELSE 0 END) as pages_with_h1,
                SUM(CASE WHEN pm.h1     IS NULL     OR  pm.h1     = ''  THEN 1 ELSE 0 END) as pages_missing_h1,
                SUM(CASE WHEN pm.meta_description IS NOT NULL AND pm.meta_description != '' THEN 1 ELSE 0 END) as pages_with_meta_desc,
                SUM(CASE WHEN pm.meta_description IS NULL     OR  pm.meta_description = ''  THEN 1 ELSE 0 END) as pages_missing_meta_desc,
                SUM(CASE WHEN pm.schema IS NOT NULL
                              AND pm.schema NOT IN ('null', '[]', '')
                         THEN 1 ELSE 0 END) as pages_with_schema
            ")
            ->first();

        // Average word count from url_inventory (stored by crawler per page)
        $avgWordCount = (float) DB::table('url_inventory')
            ->where('site_id', $siteId)
            ->where('status', 'completed')
            ->where('word_count', '>', 0)
            ->avg('word_count');

        // Internal link metrics stored directly on url_inventory rows
        $linkStats = DB::table('url_inventory')
            ->where('site_id', $siteId)
            ->where('status', 'completed')
            ->selectRaw("
                SUM(internal_link_count)  as total_outgoing,
                SUM(incoming_link_count)  as total_incoming,
                AVG(internal_link_count)  as avg_outgoing,
                AVG(incoming_link_count)  as avg_incoming,
                SUM(CASE WHEN is_orphan_page = 1 THEN 1 ELSE 0 END) as orphan_pages
            ")
            ->first();

        // Crawl depth distribution (depth 0 = homepage)
        $depthDist = DB::table('url_inventory')
            ->where('site_id', $siteId)
            ->where('status', 'completed')
            ->selectRaw('depth, COUNT(*) as count')
            ->groupBy('depth')
            ->orderBy('depth')
            ->pluck('count', 'depth')
            ->map(fn($c) => (int) $c)
            ->toArray();

        // Page type distribution
        $pageTypeCounts = DB::table('url_inventory')
            ->where('site_id', $siteId)
            ->where('status', 'completed')
            ->selectRaw('page_type, COUNT(*) as count')
            ->groupBy('page_type')
            ->orderByRaw('COUNT(*) DESC')
            ->pluck('count', 'page_type')
            ->map(fn($c) => (int) $c)
            ->toArray();

        $pagesWithMeta = (int) ($metaStats->pages_with_meta ?? 0);
        $pagesWithSchema = (int) ($metaStats->pages_with_schema ?? 0);

        return [
            // Volume
            'total_discovered' => $totalDiscovered,
            'total_crawled' => $totalCrawled,
            'indexable_pages' => $indexable,

            // Title
            'pages_with_title' => (int) ($metaStats->pages_with_title ?? 0),
            'pages_missing_title' => (int) ($metaStats->pages_missing_title ?? 0),

            // H1
            'pages_with_h1' => (int) ($metaStats->pages_with_h1 ?? 0),
            'pages_missing_h1' => (int) ($metaStats->pages_missing_h1 ?? 0),

            // Meta description
            'pages_with_meta_desc' => (int) ($metaStats->pages_with_meta_desc ?? 0),
            'pages_missing_meta_desc' => (int) ($metaStats->pages_missing_meta_desc ?? 0),

            // Schema
            'pages_with_schema' => $pagesWithSchema,
            'schema_coverage_pct' => $pagesWithMeta > 0
                ? round($pagesWithSchema / $pagesWithMeta * 100, 1)
                : 0.0,

            // Content depth
            'avg_word_count' => (int) round($avgWordCount),

            // Internal linking
            'total_outgoing_links' => (int) ($linkStats->total_outgoing ?? 0),
            'total_incoming_links' => (int) ($linkStats->total_incoming ?? 0),
            'avg_outgoing_links' => round((float) ($linkStats->avg_outgoing ?? 0), 1),
            'avg_incoming_links' => round((float) ($linkStats->avg_incoming ?? 0), 1),
            'orphan_pages' => (int) ($linkStats->orphan_pages ?? 0),

            // Structural maps
            'depth_distribution' => $depthDist,
            'page_type_counts' => $pageTypeCounts,

            // State
            'crawl_status' => $site->crawl_status ?? 'idle',
            'is_partial' => $totalCrawled < $totalDiscovered,
        ];
    }
}
