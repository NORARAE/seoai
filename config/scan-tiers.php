<?php

/**
 * Scan Tier Configuration — Progressive Report Layers
 *
 * The scanner (QuickScanService) is tier-agnostic — these settings
 * control output depth, page limits, and delivery per layer.
 * Each layer builds on the previous, unlocking deeper analysis.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Layer 1 — Base Scan ($2)
    |--------------------------------------------------------------------------
    */
    'base-scan' => [
        'price' => 200, // cents
        'label' => 'Base Scan',
        'crawl_depth' => 1,           // homepage only
        'max_pages' => 1,           // single page analyzed
        'output_detail' => 'limited',   // score + category bars + limited checks
        'competitor_compare' => false,
        'downloadable_report' => false,
        'dashboard_retention' => false,      // guest-only unless they save via Google
        'prioritization' => false,
        'email_summary' => true,        // basic score email
        'locked_sections' => ['check_details', 'fastest_fix_detail', 'fix_queue', 'market_analysis'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layer 2 — Diagnostic Expansion ($99)
    |--------------------------------------------------------------------------
    | Full signal-by-signal breakdown. Auto-delivered instantly.
    */
    'diagnostic' => [
        'price' => 9900, // cents
        'label' => 'Diagnostic Expansion',
        'crawl_depth' => 2,           // homepage + internal links (1 hop)
        'max_pages' => 20,          // up to 20 discovered pages analyzed
        'output_detail' => 'full',      // all category checks unlocked, full explanations
        'competitor_compare' => false,
        'downloadable_report' => true,       // PDF generation
        'dashboard_retention' => true,       // saved to user dashboard
        'prioritization' => true,        // findings sorted by impact
        'email_summary' => true,        // detailed email with key findings
        'locked_sections' => ['fix_queue', 'market_analysis'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layer 3 — Execution Layer ($249)
    |--------------------------------------------------------------------------
    | Priority fix queue with actionable guidance.
    */
    'fix-strategy' => [
        'price' => 24900, // cents
        'label' => 'Execution Layer',
        'crawl_depth' => 2,
        'max_pages' => 30,
        'output_detail' => 'actionable', // full + fix prioritization + implementation steps
        'competitor_compare' => false,
        'downloadable_report' => true,
        'dashboard_retention' => true,
        'prioritization' => true,
        'fix_queue' => true,        // ordered fix recommendations
        'implementation_hints' => true,      // step-by-step guidance (abstract)
        'email_summary' => true,
        'locked_sections' => ['market_analysis'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layer 4 — Full System Strategy ($489+)
    |--------------------------------------------------------------------------
    | Competitor benchmarks, opportunity mapping, expansion roadmap.
    */
    'optimization' => [
        'price_from' => 48900, // cents
        'price_to' => 68900, // cents
        'label' => 'Full System Strategy',
        'crawl_depth' => 3,           // deeper internal link traversal
        'max_pages' => 50,          // up to 50 pages analyzed
        'output_detail' => 'expert',    // full + interpretation + opportunity mapping
        'competitor_compare' => true,        // market leader comparison
        'downloadable_report' => true,       // comprehensive PDF
        'dashboard_retention' => true,
        'prioritization' => true,
        'fix_queue' => true,
        'opportunity_map' => true,        // structural opportunity map
        'expansion_framing' => true,        // expansion opportunity framing
        'email_summary' => true,
        'locked_sections' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layer 5 — Full System ($4,799+) — Reserved
    |--------------------------------------------------------------------------
    */
    'full-system' => [
        'price_from' => 479900, // cents
        'label' => 'Full System',
        'crawl_depth' => null,        // unlimited
        'max_pages' => null,        // unlimited
        'output_detail' => 'complete',
        'competitor_compare' => true,
        'downloadable_report' => true,
        'dashboard_retention' => true,
        'prioritization' => true,
        'fix_queue' => true,
        'opportunity_map' => true,
        'expansion_framing' => true,
        'implementation' => true,        // actual build + deployment
        'email_summary' => true,
        'locked_sections' => [],
    ],

];
