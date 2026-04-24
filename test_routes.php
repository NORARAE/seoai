<?php

$base = 'http://127.0.0.1:8001';

$routes = [
    // Core public pages
    '/',
    '/pricing',
    '/how-it-works',
    '/solutions',
    '/solutions/agencies',
    '/solutions/business-owners',
    '/about',
    '/growth-services',
    '/web-design-development',
    '/wordpress-support',
    '/ads-management',
    '/branding-print',
    '/access-plans',
    '/for-agencies',
    '/rd-tax-credit',
    '/ai-citation-tracking',
    '/ai-seo-for-chatgpt-geo-aeo',
    '/what-is-ai-search-optimization',
    '/ai-search-optimization',
    '/ai-search-optimization-guide',
    '/ai-citation-engine',
    '/how-ai-search-works',
    '/how-ai-retrieves-content',
    '/how-chatgpt-chooses-sources',
    '/optimize-for-ai-answers',
    '/programmatic-seo-platform',
    '/chatgpt-seo',
    '/local-ai-search',
    '/search-presence-engine',
    '/generative-engine-optimization',
    '/entity-seo-for-ai-search',
    '/aeo-vs-seo-vs-geo',
    '/ai-seo-for-local-businesses',
    '/privacy',
    '/terms',
    '/login',
    '/register',
    // Conversion pages
    '/book',
    '/quick-scan',
    '/scan/start',
    '/checkout/success',
    '/checkout/cancelled',
    '/checkout/scan-basic',
    '/checkout/signal-expansion',
    '/checkout/structural-leverage',
    '/checkout/system-activation',
    // Sitemap
    '/sitemap.xml',
    '/sitemaps/marketing-core.xml',
    '/sitemaps/marketing-agency.xml',
    '/sitemaps/marketing-local.xml',
    '/sitemaps/marketing-strategy.xml',
    '/sitemaps/marketing-industry.xml',
    // Wildcard / test slug that should 404 fallback
    '/nonexistent-page-xyz',
];

$issues = [];

foreach ($routes as $path) {
    $url = $base . $path;
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'follow_location' => 0,
            'timeout' => 10,
            'ignore_errors' => true,
        ]
    ]);

    $response = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];
    $status = isset($headers[0]) ? (int) preg_replace('/\D/', '', substr($headers[0], 9, 3)) : 0;

    // Extract just status line
    $statusLine = $headers[0] ?? 'NO_RESPONSE';

    $flag = match (true) {
        $status === 200 => '✓',
        $status >= 300 && $status < 400 => '→',
        $status === 404 => '✗ 404',
        $status === 500 => '✗ 500',
        $status === 0 => '✗ NO_CONN',
        default => "? $status",
    };

    printf("%-55s %s\n", $path, $flag . " ($status)");

    if ($status !== 200 && !($status >= 300 && $status < 400)) {
        $issues[] = "$path → $status";
    }
}

echo "\n";
if (empty($issues)) {
    echo "All routes returned 200 or redirect. No broken routes found.\n";
} else {
    echo "ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "  $issue\n";
    }
}
