<?php
// Adds FrontendDevRestricted trait to Filament Resources and Pages.
// Idempotent: skips files already patched.

$files = [
    'app/Filament/Resources/BaselineSnapshotResource.php',
    'app/Filament/Resources/Clients/ClientResource.php',
    'app/Filament/Resources/CrawlPolicyResource.php',
    'app/Filament/Resources/CrawlQueueResource.php',
    'app/Filament/Resources/InquiryResource.php',
    'app/Filament/Resources/LicenseResource.php',
    'app/Filament/Resources/LinkSuggestions/LinkSuggestionResource.php',
    'app/Filament/Resources/LocationPages/LocationPageResource.php',
    'app/Filament/Resources/Opportunities/OpportunityResource.php',
    'app/Filament/Resources/OptimizationRunResource.php',
    'app/Filament/Resources/PageGenerationBatchResource.php',
    'app/Filament/Resources/PagePayloadResource.php',
    'app/Filament/Resources/PerformanceMetricResource.php',
    'app/Filament/Resources/PublishingLogResource.php',
    'app/Filament/Resources/ScanRunResource.php',
    'app/Filament/Resources/SeoMarketingPageResource.php',
    'app/Filament/Resources/SeoOpportunityResource.php',
    'app/Filament/Resources/SiteCrawlSettingResource.php',
    'app/Filament/Resources/Sites/SiteResource.php',
    'app/Filament/Resources/UrlInventoryResource.php',
    'app/Filament/Resources/UserResource.php',
    'app/Filament/Pages/CoverageMap.php',
    'app/Filament/Pages/HelpGuides.php',
    'app/Filament/Pages/SeoGrowthCommandCenter.php',
];

$import = 'use App\\Filament\\Concerns\\FrontendDevRestricted;';
$traitUse = '    use FrontendDevRestricted;';

foreach ($files as $rel) {
    $path = getcwd() . '/' . $rel;
    if (!file_exists($path)) {
        echo "SKIP (not found): $rel\n";
        continue;
    }
    $content = file_get_contents($path);

    if (str_contains($content, 'FrontendDevRestricted')) {
        echo "SKIP (already patched): $rel\n";
        continue;
    }

    // Step 1: insert trait use statement inside the class body
    $content = preg_replace(
        '/^(class\s+\w+\s+extends\s+\S+[^\n]*\n\{)/m',
        '$1' . "\n" . $traitUse . "\n",
        $content,
        1,
        $count
    );
    if ($count === 0) {
        echo "ERROR (no class match): $rel\n";
        continue;
    }

    // Step 2: insert import after the namespace line
    $content = preg_replace(
        '/^(namespace\s+[^;]+;\n)/m',
        '$1' . "\n" . $import . "\n",
        $content,
        1,
        $count2
    );
    if ($count2 === 0) {
        echo "ERROR (no namespace match): $rel\n";
        continue;
    }

    file_put_contents($path, $content);
    echo "PATCHED: $rel\n";
}
echo "Done.\n";
