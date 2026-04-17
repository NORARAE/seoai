<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuickScanService
{
    /**
     * Run all checks on the given URL and return a structured result.
     *
     * @param  string  $url  The fully-qualified URL to scan
     * @return array{score:int, categories:array, issues:array, strengths:array, fastest_fix:string, raw_checks:array, broken_links:array, page_count:int, error:string|null}
     */
    public function scan(string $url): array
    {
        $ssrfBlock = $this->guardSsrf($url);
        if ($ssrfBlock) {
            return $ssrfBlock;
        }

        $fetch = $this->fetchUrl($url);
        if ($fetch['html'] === null) {
            return [
                'score' => 0,
                'categories' => [],
                'issues' => ['We could not fetch your website. Please check the URL and try again.'],
                'strengths' => [],
                'fastest_fix' => 'Ensure your website is publicly accessible and try again.',
                'raw_checks' => [],
                'broken_links' => [],
                'page_count' => 0,
                'error' => $fetch['error'],
            ];
        }

        $html = $fetch['html'];
        $responseTime = $fetch['time'];

        // Score all six categories
        $categories = [
            'coverage' => $this->scoreCoverage($html),
            'schema' => $this->scoreSchema($html),
            'entity_clarity' => $this->scoreEntityClarity($html),
            'internal_linking' => $this->scoreInternalLinking($html, $url),
            'extractable_content' => $this->scoreExtractableContent($html),
            'crawlability' => $this->scoreCrawlability($html, $url, $responseTime),
        ];

        $score = min(100, collect($categories)->sum('score'));

        // Build flat issues/strengths from check details
        $issues = [];
        $strengths = [];
        $firstFix = null;

        // Output variability: deterministic seed from URL for consistent-per-URL rotation
        $variantSeed = crc32($url);

        foreach ($categories as $cat) {
            foreach ($cat['checks'] as $check) {
                if ($check['passed']) {
                    $strengths[] = $this->rotatePhrase($check['pass'], $check['key'], $variantSeed, 'pass');
                } else {
                    $issues[] = $this->rotatePhrase($check['fail'], $check['key'], $variantSeed, 'fail');
                    if ($firstFix === null) {
                        $firstFix = $check['fix'];
                    }
                }
            }
        }

        // Shuffle issue ordering for variability (deterministic per URL)
        if (count($issues) > 1) {
            mt_srand($variantSeed);
            shuffle($issues);
            mt_srand();
        }

        // Shuffle category display order for variability (deterministic per URL)
        $catKeys = array_keys($categories);
        mt_srand($variantSeed + 7);
        shuffle($catKeys);
        mt_srand();
        $shuffledCategories = [];
        foreach ($catKeys as $k) {
            $shuffledCategories[$k] = $categories[$k];
        }
        $categories = $shuffledCategories;

        // 404 detection on internal links
        $linkAudit = $this->auditInternalLinks($html, $url);

        if (!empty($linkAudit['broken'])) {
            $count = count($linkAudit['broken']);
            $issues[] = "{$count} broken connection(s) detected — these disrupt AI traversal of your site.";
        }

        // Backward-compatible raw_checks (legacy consumers)
        $rawChecks = [
            'schema_present' => ($categories['schema']['score'] ?? 0) > 0,
            'faq_present' => false,
            'definition_present' => false,
            'internal_links' => ($categories['internal_linking']['score'] ?? 0) >= 8,
            'content_length' => false,
        ];
        foreach (($categories['extractable_content']['checks'] ?? []) as $check) {
            if ($check['key'] === 'faq' && $check['passed']) {
                $rawChecks['faq_present'] = true;
            }
            if ($check['key'] === 'definitions' && $check['passed']) {
                $rawChecks['definition_present'] = true;
            }
        }
        foreach (($categories['coverage']['checks'] ?? []) as $check) {
            if ($check['key'] === 'word_count' && $check['passed']) {
                $rawChecks['content_length'] = true;
            }
        }

        return [
            'score' => $score,
            'categories' => $categories,
            'issues' => $issues,
            'strengths' => $strengths,
            'fastest_fix' => $firstFix ?? 'Strong foundation detected. Consider expanding coverage to capture more of your market.',
            'raw_checks' => $rawChecks,
            'broken_links' => $linkAudit['broken'],
            'page_count' => $linkAudit['total'],
            'error' => null,
            'dimensions' => $this->buildDimensions($html, $url, $responseTime, $categories),
            'intelligence' => $this->buildScanIntelligence($categories),
        ];
    }

    // =========================================================================
    // SSRF Guard
    // =========================================================================

    private function guardSsrf(string $url): ?array
    {
        $host = parse_url($url, PHP_URL_HOST);
        $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        $block = function (string $reason) use ($url) {
            Log::warning("QuickScanService: SSRF blocked — {$reason}", ['url' => $url]);
            return [
                'score' => 0,
                'categories' => [],
                'issues' => ['This URL resolves to a private or reserved network address and cannot be scanned.'],
                'strengths' => [],
                'fastest_fix' => 'Ensure your website is hosted on a public IP address.',
                'raw_checks' => [],
                'broken_links' => [],
                'page_count' => 0,
                'error' => "SSRF protection: {$reason}",
            ];
        };

        if ($host && !filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = @gethostbynamel(strtolower($host));
            if ($ips) {
                foreach ($ips as $ip) {
                    if (!filter_var($ip, FILTER_VALIDATE_IP, $flags)) {
                        return $block('private IP resolved');
                    }
                }
            }
        } elseif ($host && filter_var($host, FILTER_VALIDATE_IP)) {
            if (!filter_var($host, FILTER_VALIDATE_IP, $flags)) {
                return $block('direct private IP');
            }
        }

        return null;
    }

    // =========================================================================
    // URL Fetch
    // =========================================================================

    private function fetchUrl(string $url): array
    {
        $tryFetch = function (string $fetchUrl) {
            $start = microtime(true);
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'SEOAIco-Scanner/1.0 (+https://seoaico.com)'])
                    ->get($fetchUrl);
                $time = round(microtime(true) - $start, 2);
                if ($response->successful()) {
                    return ['html' => $response->body(), 'time' => $time, 'error' => null];
                }
                return ['html' => null, 'time' => $time, 'error' => 'HTTP ' . $response->status()];
            } catch (\Throwable $e) {
                return ['html' => null, 'time' => round(microtime(true) - $start, 2), 'error' => Str::limit($e->getMessage(), 120)];
            }
        };

        $result = $tryFetch($url);
        if ($result['html'] !== null) {
            return $result;
        }

        // Fallback: try http:// if https:// failed
        if (str_starts_with($url, 'https://')) {
            $httpResult = $tryFetch('http://' . substr($url, 8));
            if ($httpResult['html'] !== null) {
                return $httpResult;
            }
        }

        return $result;
    }

    // =========================================================================
    // Scoring Categories
    // =========================================================================

    /**
     * Coverage (20 pts max) — Content depth, structure, meta tags
     */
    private function scoreCoverage(string $html): array
    {
        $checks = [];

        // Title tag (4 pts)
        $hasTitle = (bool) preg_match('/<title[^>]*>.+<\/title>/is', $html);
        $checks[] = [
            'key' => 'title',
            'passed' => $hasTitle,
            'points' => $hasTitle ? 4 : 0,
            'max' => 4,
            'label' => 'Primary Topic Signal',
            'pass' => 'Primary topic signal detected — AI systems can identify this page.',
            'fail' => 'Primary topic signal missing — AI systems cannot determine what this page covers.',
            'fix' => 'Add a primary topic signal that includes your core service and location.',
        ];

        // Meta description (4 pts)
        $hasDesc = (bool) preg_match('/<meta\s[^>]*name=["\']description["\'][^>]*content=["\'].+["\']/is', $html)
            || (bool) preg_match('/<meta\s[^>]*content=["\'].+["\']\s[^>]*name=["\']description["\']/is', $html);
        $checks[] = [
            'key' => 'meta_description',
            'passed' => $hasDesc,
            'points' => $hasDesc ? 4 : 0,
            'max' => 4,
            'label' => 'Page Summary Signal',
            'pass' => 'Page summary signal present — AI systems have context for this page.',
            'fail' => 'No page summary signal — AI systems have no context for this page.',
            'fix' => 'Add a page summary signal that describes your core offering.',
        ];

        // H1 tag (4 pts)
        $hasH1 = (bool) preg_match('/<h1[\s>]/i', $html);
        $checks[] = [
            'key' => 'h1',
            'passed' => $hasH1,
            'points' => $hasH1 ? 4 : 0,
            'max' => 4,
            'label' => 'Primary Content Marker',
            'pass' => 'Primary content marker detected — clear topic established.',
            'fail' => 'No primary content marker — the page lacks a clear topic signal.',
            'fix' => 'Add a primary content marker that states your core service or business name.',
        ];

        // H2 sections (4 pts for 2+)
        preg_match_all('/<h2[\s>]/i', $html, $h2s);
        $h2Count = count($h2s[0] ?? []);
        $hasH2s = $h2Count >= 2;
        $checks[] = [
            'key' => 'h2_sections',
            'passed' => $hasH2s,
            'points' => $hasH2s ? 4 : 0,
            'max' => 4,
            'label' => 'Topic Depth Markers',
            'pass' => "Multiple topic depth markers detected ({$h2Count}) — strong structure.",
            'fail' => 'Insufficient topic depth markers — AI cannot map your content structure.',
            'fix' => 'Add topic depth markers covering different aspects of your service.',
        ];

        // Word count (4 pts for 300+)
        $text = preg_replace('/\s+/', ' ', strip_tags($html));
        $wordCount = str_word_count(trim($text));
        $hasWords = $wordCount >= 300;
        $checks[] = [
            'key' => 'word_count',
            'passed' => $hasWords,
            'points' => $hasWords ? 4 : 0,
            'max' => 4,
            'label' => 'Content Substance',
            'pass' => "Sufficient content substance ({$wordCount} words) — AI systems have enough to analyze.",
            'fail' => "Insufficient content substance ({$wordCount} words) — AI systems deprioritize thin pages.",
            'fix' => 'Expand content substance to cover your services and service area comprehensively.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 20, 'label' => 'AI Readability Signals', 'checks' => $checks];
    }

    /**
     * Schema (25 pts max) — Structured data presence and quality
     */
    private function scoreSchema(string $html): array
    {
        $checks = [];

        // JSON-LD present (10 pts)
        $hasJsonLd = str_contains($html, 'application/ld+json');
        $checks[] = [
            'key' => 'json_ld',
            'passed' => $hasJsonLd,
            'points' => $hasJsonLd ? 10 : 0,
            'max' => 10,
            'label' => 'Structured Data Layer',
            'pass' => 'Structured data layer detected — AI systems can parse your business information.',
            'fail' => 'No structured data layer — AI systems cannot identify your business type or services.',
            'fix' => 'Implement a structured data layer that defines your business type and services.',
        ];

        // Has @type (5 pts)
        $hasType = $hasJsonLd && str_contains($html, '"@type"');
        $checks[] = [
            'key' => 'schema_type',
            'passed' => $hasType,
            'points' => $hasType ? 5 : 0,
            'max' => 5,
            'label' => 'Entity Type Declaration',
            'pass' => 'Entity type declaration found — your business type is machine-readable.',
            'fail' => 'No entity type declaration — AI cannot classify your business.',
            'fix' => 'Add an entity type declaration that classifies your business category.',
        ];

        // Business entity type (5 pts)
        $entityTypes = [
            'Organization',
            'LocalBusiness',
            'ProfessionalService',
            'MedicalBusiness',
            'LegalService',
            'FinancialService',
            'RealEstateAgent',
            'HomeAndConstructionBusiness',
            'Store',
            'Restaurant',
            'AutoRepair',
            'Service'
        ];
        $hasEntity = false;
        foreach ($entityTypes as $type) {
            if (str_contains($html, $type)) {
                $hasEntity = true;
                break;
            }
        }
        $checks[] = [
            'key' => 'entity_schema',
            'passed' => $hasEntity,
            'points' => $hasEntity ? 5 : 0,
            'max' => 5,
            'label' => 'Business Category Signal',
            'pass' => 'Business category signal detected — AI can identify your industry.',
            'fail' => 'No business category signal — AI cannot determine your industry.',
            'fix' => 'Add a specific business category signal that identifies your industry.',
        ];

        // Rich schema (multiple types or microdata) (5 pts)
        preg_match_all('/"@type"\s*:\s*"([^"]+)"/', $html, $typeMatches);
        $typeCount = count(array_unique($typeMatches[1] ?? []));
        $hasMicrodata = (bool) preg_match('/itemtype=["\']https?:\/\/schema\.org\//i', $html);
        $richSchema = $typeCount >= 2 || ($hasJsonLd && $hasMicrodata);
        $checks[] = [
            'key' => 'rich_schema',
            'passed' => $richSchema,
            'points' => $richSchema ? 5 : 0,
            'max' => 5,
            'label' => 'Data Depth Coverage',
            'pass' => 'Multiple data layers detected — strong machine-readable foundation.',
            'fail' => 'Limited data depth — expand with additional machine-readable layers for your services.',
            'fix' => 'Add additional data layers alongside your primary business declaration.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 25, 'label' => 'Machine-Readable Context', 'checks' => $checks];
    }

    /**
     * Entity Clarity (15 pts max) — How clearly the site identifies what it is
     */
    private function scoreEntityClarity(string $html): array
    {
        $checks = [];
        $text = strtolower(strip_tags($html));

        // Business name identifiable (5 pts)
        $hasName = (bool) preg_match('/"name"\s*:\s*"[^"]+"/i', $html)
            || (bool) preg_match('/<h1[^>]*>[^<]+<\/h1>/i', $html);
        $checks[] = [
            'key' => 'business_name',
            'passed' => $hasName,
            'points' => $hasName ? 5 : 0,
            'max' => 5,
            'label' => 'Brand Identity Signal',
            'pass' => 'Brand identity signal detected — AI can reference your business by name.',
            'fail' => 'Brand identity signal missing — AI may not recognize your business by name.',
            'fix' => 'Ensure your business name is prominently declared across key page signals.',
        ];

        // Services described (5 pts)
        $servicePatterns = ['service', 'we offer', 'we provide', 'our services', 'what we do', 'solutions', 'specializ'];
        $hasServices = false;
        foreach ($servicePatterns as $p) {
            if (str_contains($text, $p)) {
                $hasServices = true;
                break;
            }
        }
        $checks[] = [
            'key' => 'services',
            'passed' => $hasServices,
            'points' => $hasServices ? 5 : 0,
            'max' => 5,
            'label' => 'Offering Clarity',
            'pass' => 'Offering clarity detected — AI can describe what your business provides.',
            'fail' => 'No offering clarity — AI cannot describe what your business provides.',
            'fix' => 'Add clear descriptions of what your business offers using natural language.',
        ];

        // Location/area mentioned (5 pts)
        $locationPatterns = ['located in', 'serving', 'based in', 'our area', 'service area', 'near ', 'county', 'region'];
        $hasLocation = false;
        foreach ($locationPatterns as $p) {
            if (str_contains($text, $p)) {
                $hasLocation = true;
                break;
            }
        }
        if (!$hasLocation) {
            $hasLocation = (bool) preg_match('/\b\d{5}(-\d{4})?\b/', $text);
        }
        if (!$hasLocation) {
            $hasLocation = str_contains($html, '"address"') || str_contains($html, '"areaServed"');
        }
        $checks[] = [
            'key' => 'location',
            'passed' => $hasLocation,
            'points' => $hasLocation ? 5 : 0,
            'max' => 5,
            'label' => 'Geographic Context',
            'pass' => 'Geographic context detected — AI can surface you for location-relevant queries.',
            'fail' => 'No geographic context — AI cannot associate you with a service area.',
            'fix' => 'Establish geographic context by referencing your service area on the page.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 15, 'label' => 'Entity Definition Strength', 'checks' => $checks];
    }

    /**
     * Internal Linking (15 pts max) — Link structure quality (scaled)
     */
    private function scoreInternalLinking(string $html, string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        $internalLinks = [];
        foreach ($matches[1] ?? [] as $link) {
            $link = trim($link);
            if (empty($link) || $link === '#' || str_starts_with($link, 'javascript:') || str_starts_with($link, 'mailto:') || str_starts_with($link, 'tel:')) {
                continue;
            }
            if (!str_starts_with($link, 'http')) {
                $internalLinks[] = $link;
            } elseif ($host && str_contains(parse_url($link, PHP_URL_HOST) ?? '', $host)) {
                $internalLinks[] = $link;
            }
        }

        $uniqueCount = count(array_unique($internalLinks));

        if ($uniqueCount >= 20) {
            $points = 15;
        } elseif ($uniqueCount >= 10) {
            $points = 12;
        } elseif ($uniqueCount >= 5) {
            $points = 8;
        } elseif ($uniqueCount >= 1) {
            $points = 4;
        } else {
            $points = 0;
        }

        $passed = $points >= 8;
        $checks = [
            [
                'key' => 'link_count',
                'passed' => $passed,
                'points' => $points,
                'max' => 15,
                'label' => 'Content Graph Density',
                'pass' => "Strong content graph ({$uniqueCount} connections) — AI can traverse your site.",
                'fail' => $uniqueCount === 0
                    ? 'No content connections found — AI cannot discover your other pages.'
                    : "Sparse content graph ({$uniqueCount} connections) — AI cannot fully map your site.",
                'fix' => 'Connect your core pages to strengthen AI discovery of your full site.',
            ]
        ];

        return ['score' => $points, 'max' => 15, 'label' => 'Site Connectivity', 'checks' => $checks];
    }

    /**
     * Extractable Content (15 pts max) — AI-citable content
     */
    private function scoreExtractableContent(string $html): array
    {
        $checks = [];
        $lower = strtolower($html);

        // FAQ / Q&A (5 pts)
        $faqPatterns = ['faq', 'frequently asked', '<details', '<dl>', '<dt>', 'how do i', 'how does'];
        $hasFaq = false;
        foreach ($faqPatterns as $p) {
            if (str_contains($lower, $p)) {
                $hasFaq = true;
                break;
            }
        }
        if (!$hasFaq) {
            $hasFaq = str_contains($html, 'FAQPage');
        }
        $checks[] = [
            'key' => 'faq',
            'passed' => $hasFaq,
            'points' => $hasFaq ? 5 : 0,
            'max' => 5,
            'label' => 'Direct Answer Content',
            'pass' => 'Direct answer content detected — AI can extract citable responses from your pages.',
            'fail' => 'No direct answer content — AI has no citable responses to extract.',
            'fix' => 'Add direct answer content addressing the questions your customers ask most.',
        ];

        // Definitions / explanations (5 pts)
        $defPatterns = ['what is ', 'what are ', 'how does ', 'how do ', 'defined as', 'refers to', 'is a service', 'is the process'];
        $hasDefs = false;
        foreach ($defPatterns as $p) {
            if (str_contains($lower, $p)) {
                $hasDefs = true;
                break;
            }
        }
        $checks[] = [
            'key' => 'definitions',
            'passed' => $hasDefs,
            'points' => $hasDefs ? 5 : 0,
            'max' => 5,
            'label' => 'Authoritative Definitions',
            'pass' => 'Authoritative definitions detected — your site frames topics AI references.',
            'fail' => 'No authoritative definitions — AI cannot cite your site as a reference source.',
            'fix' => 'Add authoritative definitions that establish your expertise on core topics.',
        ];

        // Structured lists (5 pts)
        preg_match_all('/<(?:ul|ol)[^>]*>.*?<\/(?:ul|ol)>/is', $html, $listMatches);
        $listCount = 0;
        foreach ($listMatches[0] ?? [] as $list) {
            if (substr_count(strtolower($list), '<li') >= 3) {
                $listCount++;
            }
        }
        $hasLists = $listCount >= 1;
        $checks[] = [
            'key' => 'structured_lists',
            'passed' => $hasLists,
            'points' => $hasLists ? 5 : 0,
            'max' => 5,
            'label' => 'Organized Content Blocks',
            'pass' => 'Organized content blocks detected — AI can extract structured information.',
            'fail' => 'No organized content blocks — structured information improves AI extractability.',
            'fix' => 'Add organized content blocks listing your services, benefits, or process steps.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 15, 'label' => 'Content Structure Integrity', 'checks' => $checks];
    }

    /**
     * Crawlability (10 pts max) — Technical accessibility
     */
    private function scoreCrawlability(string $html, string $url, float $responseTime): array
    {
        $checks = [];

        // HTTPS (3 pts)
        $isHttps = str_starts_with($url, 'https://');
        $checks[] = [
            'key' => 'https',
            'passed' => $isHttps,
            'points' => $isHttps ? 3 : 0,
            'max' => 3,
            'label' => 'Secure Connection',
            'pass' => 'Secure connection verified — trust signal established.',
            'fail' => 'No secure connection — trust signals reduced for AI and users.',
            'fix' => 'Enable a secure connection on your website.',
        ];

        // Response time < 3s (3 pts)
        $fast = $responseTime < 3.0;
        $checks[] = [
            'key' => 'response_time',
            'passed' => $fast,
            'points' => $fast ? 3 : 0,
            'max' => 3,
            'label' => 'Access Speed',
            'pass' => "Fast access ({$responseTime}s) — AI systems can efficiently reach your content.",
            'fail' => "Slow access ({$responseTime}s) — AI systems may deprioritize your site.",
            'fix' => 'Improve access speed to under 3 seconds.',
        ];

        // Not noindex (2 pts)
        $hasNoindex = (bool) preg_match('/<meta[^>]*name=["\']robots["\'][^>]*content=["\'][^"\']*noindex/i', $html);
        $indexable = !$hasNoindex;
        $checks[] = [
            'key' => 'indexable',
            'passed' => $indexable,
            'points' => $indexable ? 2 : 0,
            'max' => 2,
            'label' => 'Discoverability',
            'pass' => 'Page is discoverable — no access restrictions detected.',
            'fail' => 'Page is blocked from discovery — AI systems will skip this page entirely.',
            'fix' => 'Remove the discovery restriction to allow AI systems to access and cite your content.',
        ];

        // Canonical URL (2 pts)
        $hasCanonical = (bool) preg_match('/<link[^>]*rel=["\']canonical["\']/i', $html);
        $checks[] = [
            'key' => 'canonical',
            'passed' => $hasCanonical,
            'points' => $hasCanonical ? 2 : 0,
            'max' => 2,
            'label' => 'Authority Consolidation',
            'pass' => 'Authority consolidation set — prevents duplicate content dilution.',
            'fail' => 'No authority consolidation — duplicate content may dilute your citation strength.',
            'fix' => 'Add authority consolidation pointing to the preferred version of this page.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 10, 'label' => 'Technical Accessibility', 'checks' => $checks];
    }

    // =========================================================================
    // Internal Link 404 Audit
    // =========================================================================

    private function auditInternalLinks(string $html, string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $scheme = parse_url($url, PHP_URL_SCHEME) ?? 'https';
        $baseUrl = "{$scheme}://{$host}";

        if (!$host) {
            return ['broken' => [], 'total' => 0];
        }

        preg_match_all('/<a\s[^>]*href=["\']([^"\'#]+)["\'][^>]*>/i', $html, $matches);
        $seen = [];
        $uniqueLinks = [];

        foreach ($matches[1] ?? [] as $link) {
            $link = trim($link);
            if (empty($link) || str_starts_with($link, 'javascript:') || str_starts_with($link, 'mailto:') || str_starts_with($link, 'tel:')) {
                continue;
            }

            if (!str_starts_with($link, 'http')) {
                $link = rtrim($baseUrl, '/') . '/' . ltrim($link, '/');
            }

            $linkHost = parse_url($link, PHP_URL_HOST) ?? '';
            if (!str_contains($linkHost, $host)) {
                continue;
            }

            $normalized = rtrim(strtolower($link), '/');
            if (isset($seen[$normalized])) {
                continue;
            }
            $seen[$normalized] = true;
            $uniqueLinks[] = $link;
        }

        $total = count($uniqueLinks);
        if ($total === 0) {
            return ['broken' => [], 'total' => 0];
        }

        $toCheck = array_values(array_slice($uniqueLinks, 0, 20));
        $broken = [];

        try {
            $responses = Http::pool(function (Pool $pool) use ($toCheck) {
                $pending = [];
                foreach ($toCheck as $i => $link) {
                    $pending[] = $pool->as("l{$i}")
                        ->timeout(5)
                        ->withHeaders(['User-Agent' => 'SEOAIco-Scanner/1.0 (+https://seoaico.com)'])
                        ->head($link);
                }
                return $pending;
            });

            foreach ($toCheck as $i => $link) {
                try {
                    $resp = $responses["l{$i}"] ?? null;
                    if ($resp instanceof \Illuminate\Http\Client\Response) {
                        $status = $resp->status();
                        if ($status === 404 || $status === 410 || $status >= 500) {
                            $broken[] = ['url' => $link, 'status' => $status];
                        }
                    }
                } catch (\Throwable $e) {
                    // Connection errors are not 404s
                }
            }
        } catch (\Throwable $e) {
            Log::warning('QuickScanService: link audit pool failed', ['url' => $url, 'error' => $e->getMessage()]);
        }

        return ['broken' => $broken, 'total' => $total];
    }

    // =========================================================================
    // Expanded Scan Dimensions (Steps 3 + 4 + 5)
    // =========================================================================

    /**
     * Build the 6 expanded scan dimensions from HTML analysis.
     * Each dimension maps findings to fix tiers ($99/$249/$489).
     */
    private function buildDimensions(string $html, string $url, float $responseTime, array $categories): array
    {
        $lower = strtolower($html);
        $text = strtolower(strip_tags($html));

        return [
            'citation_readiness' => $this->dimensionCitationReadiness($html, $categories),
            'content_extraction' => $this->dimensionContentExtraction($html, $categories),
            'service_location_coverage' => $this->dimensionServiceLocationCoverage($html, $text, $url),
            'internal_linking' => $this->dimensionInternalLinking($html, $url, $categories),
            'authority_signals' => $this->dimensionAuthoritySignals($html, $text, $lower),
            'technical_signals' => $this->dimensionTechnicalSignals($html, $url, $responseTime, $categories),
        ];
    }

    /**
     * Citation Readiness — Can AI systems cite this business?
     */
    private function dimensionCitationReadiness(string $html, array $categories): array
    {
        $findings = [];
        $score = 0;
        $max = 0;

        // Pull from existing schema + entity clarity checks
        foreach (['schema', 'entity_clarity'] as $catKey) {
            if (!isset($categories[$catKey]))
                continue;
            foreach ($categories[$catKey]['checks'] as $check) {
                $max += $check['max'];
                $score += $check['points'];
                if (!$check['passed']) {
                    $findings[] = [
                        'what_missing' => $check['label'] . ' — ' . $check['fail'],
                        'why_it_matters' => $this->citationContext($check['key']),
                        'fix_tier' => 'signal-expansion',
                        'fix_price' => '$99',
                        'fix_route' => 'checkout.signal-expansion',
                    ];
                }
            }
        }

        // Additional: Open Graph / social meta
        $hasOg = (bool) preg_match('/<meta\s[^>]*property=["\']og:/i', $html);
        $max += 5;
        if ($hasOg) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Social Citation Layer — No Open Graph metadata detected.',
                'why_it_matters' => 'AI systems and social platforms use Open Graph to generate previews. Without it, your content appears generic when shared or cited.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        return [
            'label' => 'Citation Readiness',
            'score' => $score,
            'max' => $max,
            'pct' => $max > 0 ? round($score / $max * 100) : 0,
            'findings' => $findings,
        ];
    }

    /**
     * Content Extraction Structure — Can AI extract useful content?
     */
    private function dimensionContentExtraction(string $html, array $categories): array
    {
        $findings = [];
        $score = 0;
        $max = 0;

        foreach (['coverage', 'extractable_content'] as $catKey) {
            if (!isset($categories[$catKey]))
                continue;
            foreach ($categories[$catKey]['checks'] as $check) {
                $max += $check['max'];
                $score += $check['points'];
                if (!$check['passed']) {
                    $tier = in_array($check['key'], ['faq', 'definitions', 'structured_lists'])
                        ? 'structural-leverage'
                        : 'signal-expansion';
                    $findings[] = [
                        'what_missing' => $check['label'] . ' — ' . $check['fail'],
                        'why_it_matters' => $this->extractionContext($check['key']),
                        'fix_tier' => $tier,
                        'fix_price' => $tier === 'signal-expansion' ? '$99' : '$249',
                        'fix_route' => $tier === 'signal-expansion' ? 'checkout.signal-expansion' : 'checkout.structural-leverage',
                    ];
                }
            }
        }

        // Additional: table content
        $hasTables = (bool) preg_match('/<table[\s>]/i', $html);
        $max += 3;
        if ($hasTables) {
            $score += 3;
        } else {
            $findings[] = [
                'what_missing' => 'Structured Data Tables — No tabular content detected.',
                'why_it_matters' => 'AI systems extract comparison and specification data from tables. Without them, your structured information is harder to parse.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        }

        return [
            'label' => 'Content Extraction Structure',
            'score' => $score,
            'max' => $max,
            'pct' => $max > 0 ? round($score / $max * 100) : 0,
            'findings' => $findings,
        ];
    }

    /**
     * Service/Location Coverage — Is the business's coverage territory machine-readable?
     */
    private function dimensionServiceLocationCoverage(string $html, string $text, string $url): array
    {
        $findings = [];
        $score = 0;
        $max = 25;

        // Service page links (5 pts)
        $serviceLinks = 0;
        $servicePagePatterns = ['/service', '/what-we-do', '/solutions', '/our-work'];
        foreach ($servicePagePatterns as $pattern) {
            if (str_contains(strtolower($html), $pattern)) {
                $serviceLinks++;
            }
        }
        if ($serviceLinks > 0) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Service Page Architecture — No dedicated service page links detected.',
                'why_it_matters' => 'AI systems map your business capabilities through service-specific pages. A single page limits what queries you can appear in.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        }

        // Location/area served declarations (5 pts)
        $hasAreaServed = str_contains($html, '"areaServed"') || str_contains($html, '"serviceArea"');
        $hasLocationContent = (bool) preg_match('/\b(serving|located in|based in|service area)\b/i', $text);
        if ($hasAreaServed || $hasLocationContent) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Service Area Declaration — No service territory defined.',
                'why_it_matters' => 'AI systems match businesses to locations. Without area declarations, you miss "near me" and location-qualified queries entirely.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        // Multiple location references (5 pts)
        preg_match_all('/\b([A-Z][a-z]+(?:\s[A-Z][a-z]+)*),?\s*(?:AL|AK|AZ|AR|CA|CO|CT|DE|FL|GA|HI|ID|IL|IN|IA|KS|KY|LA|ME|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VT|VA|WA|WV|WI|WY)\b/', $html, $locMatches);
        $locationCount = count(array_unique($locMatches[0] ?? []));
        if ($locationCount >= 3) {
            $score += 5;
        } elseif ($locationCount >= 1) {
            $score += 2;
            $findings[] = [
                'what_missing' => 'Location Coverage Depth — Only ' . $locationCount . ' location(s) referenced.',
                'why_it_matters' => 'Businesses with multi-location content capture more geographic queries. Limited location references restrict your geographic reach.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        } else {
            $findings[] = [
                'what_missing' => 'Location References — No specific locations mentioned.',
                'why_it_matters' => 'Without named locations, AI cannot connect your business to geographic searches.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        // Structured address data (5 pts)
        $hasAddress = str_contains($html, '"address"') || str_contains($html, '"PostalAddress"')
            || (bool) preg_match('/<address[\s>]/i', $html);
        if ($hasAddress) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Structured Address — No machine-readable address found.',
                'why_it_matters' => 'AI systems like Google and ChatGPT use structured address data to verify business legitimacy and relevant geography.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        // GeoCoordinates (5 pts)
        $hasGeo = str_contains($html, '"GeoCoordinates"') || str_contains($html, '"geo"') || str_contains($html, '"latitude"');
        if ($hasGeo) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Geographic Coordinates — No GeoCoordinates data found.',
                'why_it_matters' => 'Precise geolocation data helps AI connect you to proximity-based queries. Without it, your local relevance is weakened.',
                'fix_tier' => 'system-activation',
                'fix_price' => '$489',
                'fix_route' => 'checkout.system-activation',
            ];
        }

        return [
            'label' => 'Service & Location Coverage',
            'score' => $score,
            'max' => $max,
            'pct' => $max > 0 ? round($score / $max * 100) : 0,
            'findings' => $findings,
        ];
    }

    /**
     * Internal Linking (expanded) — Site graph quality
     */
    private function dimensionInternalLinking(string $html, string $url, array $categories): array
    {
        $findings = [];
        $score = 0;
        $max = 25;

        // Base internal linking from existing check
        $linkScore = $categories['internal_linking']['score'] ?? 0;
        $score += min(15, $linkScore);

        if ($linkScore < 8) {
            $findings[] = [
                'what_missing' => 'Content Graph Density — Internal link network is thin.',
                'why_it_matters' => 'AI systems discover pages through internal links. A weak link network means most of your content is invisible.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        }

        // Navigation structure (5 pts)
        $hasNav = (bool) preg_match('/<nav[\s>]/i', $html);
        if ($hasNav) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Navigation Architecture — No semantic navigation element detected.',
                'why_it_matters' => 'Semantic navigation tells AI which pages are most important. Without it, crawlers cannot prioritize your content hierarchy.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        }

        // Breadcrumbs (5 pts)
        $hasBreadcrumbs = str_contains($html, 'BreadcrumbList')
            || (bool) preg_match('/class=["\'][^"\']*breadcrumb/i', $html)
            || str_contains($html, 'aria-label="breadcrumb"');
        if ($hasBreadcrumbs) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Breadcrumb Navigation — No breadcrumb trail detected.',
                'why_it_matters' => 'Breadcrumbs establish content hierarchy for AI. They improve rich result eligibility and help AI understand page relationships.',
                'fix_tier' => 'system-activation',
                'fix_price' => '$489',
                'fix_route' => 'checkout.system-activation',
            ];
        }

        return [
            'label' => 'Internal Linking & Navigation',
            'score' => $score,
            'max' => $max,
            'pct' => $max > 0 ? round($score / $max * 100) : 0,
            'findings' => $findings,
        ];
    }

    /**
     * Authority Signals — Does AI recognize this source as authoritative?
     */
    private function dimensionAuthoritySignals(string $html, string $text, string $lower): array
    {
        $findings = [];
        $score = 0;
        $max = 30;

        // About page link (5 pts)
        $hasAboutLink = (bool) preg_match('/href=["\'][^"\']*\/about/i', $html);
        if ($hasAboutLink) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'About Page Reference — No link to an about/team page detected.',
                'why_it_matters' => 'AI systems look for about pages to verify entity legitimacy. Without one, your business lacks a verifiable identity layer.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        }

        // Testimonials / reviews (5 pts)
        $reviewPatterns = ['testimonial', 'review', 'client says', 'customer feedback', '"Review"', 'aggregaterating', 'ratingvalue'];
        $hasReviews = false;
        foreach ($reviewPatterns as $p) {
            if (str_contains($lower, strtolower($p))) {
                $hasReviews = true;
                break;
            }
        }
        if ($hasReviews) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Social Proof — No testimonials, reviews, or ratings detected.',
                'why_it_matters' => 'AI systems weight social proof when recommending businesses. Reviews and testimonials signal reliability to AI.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        }

        // Credential signals (5 pts)
        $credPatterns = ['certif', 'licensed', 'accredited', 'member of', 'award', 'recognized', 'years of experience', 'founded in'];
        $hasCreds = false;
        foreach ($credPatterns as $p) {
            if (str_contains($text, $p)) {
                $hasCreds = true;
                break;
            }
        }
        if ($hasCreds) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Credential Signals — No certifications, licensing, or experience markers found.',
                'why_it_matters' => 'AI systems use credential signals to rank authority. Businesses without visible credentials appear less trustworthy to AI.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        // Author / team attribution (5 pts)
        $hasAuthor = str_contains($html, '"author"')
            || (bool) preg_match('/class=["\'][^"\']*author/i', $html)
            || str_contains($lower, 'written by')
            || str_contains($lower, 'our team');
        if ($hasAuthor) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Content Attribution — No author or team attribution found.',
                'why_it_matters' => 'AI systems prefer content with clear attribution. Anonymous content scores lower in E-E-A-T evaluation.',
                'fix_tier' => 'system-activation',
                'fix_price' => '$489',
                'fix_route' => 'checkout.system-activation',
            ];
        }

        // External trust signals (5 pts)
        $trustPatterns = ['bbb', 'better business', 'yelp', 'google.com/maps', 'trustpilot', 'angi', 'homeadvisor'];
        $hasTrust = false;
        foreach ($trustPatterns as $p) {
            if (str_contains($lower, $p)) {
                $hasTrust = true;
                break;
            }
        }
        if ($hasTrust) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'External Trust References — No third-party trust platform links detected.',
                'why_it_matters' => 'AI cross-references businesses with third-party platforms. Links to review platforms and directories strengthen your authority.',
                'fix_tier' => 'system-activation',
                'fix_price' => '$489',
                'fix_route' => 'checkout.system-activation',
            ];
        }

        // Contact information richness (5 pts)
        $hasPhone = (bool) preg_match('/href=["\']tel:/i', $html) || (bool) preg_match('/\(\d{3}\)\s?\d{3}[-.]?\d{4}/', $html);
        $hasEmail = (bool) preg_match('/href=["\']mailto:/i', $html);
        $contact = ($hasPhone ? 3 : 0) + ($hasEmail ? 2 : 0);
        $score += $contact;
        if ($contact < 3) {
            $findings[] = [
                'what_missing' => 'Contact Accessibility — ' . ($hasPhone ? 'Email' : ($hasEmail ? 'Phone' : 'Phone and email')) . ' contact not detected.',
                'why_it_matters' => 'Complete contact information is a basic trust signal for AI. Missing contact methods reduce your perceived legitimacy.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        return [
            'label' => 'Authority Signals',
            'score' => $score,
            'max' => $max,
            'pct' => $max > 0 ? round($score / $max * 100) : 0,
            'findings' => $findings,
        ];
    }

    /**
     * Technical Signals — Infrastructure quality for AI access
     */
    private function dimensionTechnicalSignals(string $html, string $url, float $responseTime, array $categories): array
    {
        $findings = [];
        $score = 0;
        $max = 25;

        // Base crawlability from existing checks
        $crawlScore = $categories['crawlability']['score'] ?? 0;
        $score += min(10, $crawlScore);

        foreach (($categories['crawlability']['checks'] ?? []) as $check) {
            if (!$check['passed']) {
                $findings[] = [
                    'what_missing' => $check['label'] . ' — ' . $check['fail'],
                    'why_it_matters' => $this->technicalContext($check['key']),
                    'fix_tier' => 'signal-expansion',
                    'fix_price' => '$99',
                    'fix_route' => 'checkout.signal-expansion',
                ];
            }
        }

        // Mobile viewport (5 pts)
        $hasViewport = (bool) preg_match('/<meta[^>]*name=["\']viewport["\']/i', $html);
        if ($hasViewport) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Mobile Viewport — No mobile viewport declaration detected.',
                'why_it_matters' => 'The majority of AI search happens on mobile. Without a viewport declaration, your page renders poorly on mobile and AI systems note this.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        // Structured heading hierarchy (5 pts)
        $headingOrder = [];
        preg_match_all('/<h([1-6])[\s>]/i', $html, $hMatches);
        foreach ($hMatches[1] ?? [] as $level) {
            $headingOrder[] = (int) $level;
        }
        $hasGoodHierarchy = !empty($headingOrder) && $headingOrder[0] <= 2;
        if ($hasGoodHierarchy) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Heading Hierarchy — Page structure does not follow logical heading order.',
                'why_it_matters' => 'AI systems use heading levels to understand content relationships. Poor heading structure makes your content harder to parse.',
                'fix_tier' => 'structural-leverage',
                'fix_price' => '$249',
                'fix_route' => 'checkout.structural-leverage',
            ];
        }

        // Hreflang / language (5 pts)
        $hasLang = (bool) preg_match('/<html[^>]*lang=["\'][a-z]{2}/i', $html);
        if ($hasLang) {
            $score += 5;
        } else {
            $findings[] = [
                'what_missing' => 'Language Declaration — No html lang attribute set.',
                'why_it_matters' => 'AI systems use language declarations to serve content to the correct audience. Missing language signals can cause misclassification.',
                'fix_tier' => 'signal-expansion',
                'fix_price' => '$99',
                'fix_route' => 'checkout.signal-expansion',
            ];
        }

        return [
            'label' => 'Technical Signals',
            'score' => $score,
            'max' => $max,
            'pct' => $max > 0 ? round($score / $max * 100) : 0,
            'findings' => $findings,
        ];
    }

    /**
     * Build per-issue intelligence: group all findings by fix tier
     * and summarize what's missing, why it matters, and what level fixes it.
     */
    public function buildScanIntelligence(array $categories): array
    {
        $intelligence = [
            'signal-expansion' => [
                'tier' => 'signal-expansion',
                'label' => 'Signal Expansion',
                'price' => '$99',
                'route' => 'checkout.signal-expansion',
                'description' => 'Missing signals — your business data is not fully readable by AI systems.',
                'issues' => [],
            ],
            'structural-leverage' => [
                'tier' => 'structural-leverage',
                'label' => 'Structural Leverage',
                'price' => '$249',
                'route' => 'checkout.structural-leverage',
                'description' => 'Missing structure — your content architecture limits AI discoverability.',
                'issues' => [],
            ],
            'system-activation' => [
                'tier' => 'system-activation',
                'label' => 'System Activation',
                'price' => '$489',
                'route' => 'checkout.system-activation',
                'description' => 'Needs implementation — competitive positioning and full system deployment.',
                'issues' => [],
            ],
        ];

        // Map existing category checks to tiers
        $tierMap = [
            // Signal-level issues ($99): basic metadata, schema, entity signals
            'title' => 'signal-expansion',
            'meta_description' => 'signal-expansion',
            'h1' => 'signal-expansion',
            'json_ld' => 'signal-expansion',
            'schema_type' => 'signal-expansion',
            'entity_schema' => 'signal-expansion',
            'business_name' => 'signal-expansion',
            'services' => 'signal-expansion',
            'location' => 'signal-expansion',
            'https' => 'signal-expansion',
            'indexable' => 'signal-expansion',

            // Structure-level issues ($249): content depth, linking, organization
            'h2_sections' => 'structural-leverage',
            'word_count' => 'structural-leverage',
            'link_count' => 'structural-leverage',
            'faq' => 'structural-leverage',
            'definitions' => 'structural-leverage',
            'structured_lists' => 'structural-leverage',
            'canonical' => 'structural-leverage',

            // Implementation-level issues ($489): rich schema, speed, advanced
            'rich_schema' => 'system-activation',
            'response_time' => 'system-activation',
        ];

        foreach ($categories as $cat) {
            foreach ($cat['checks'] ?? [] as $check) {
                if ($check['passed'])
                    continue;

                $tier = $tierMap[$check['key']] ?? 'signal-expansion';
                $intelligence[$tier]['issues'][] = [
                    'key' => $check['key'],
                    'what_missing' => $check['label'],
                    'why_it_matters' => $this->aiContext($check['key']),
                    'fix' => $check['fix'] ?? 'Address this gap to improve AI visibility.',
                ];
            }
        }

        // Remove empty tiers
        return array_values(array_filter($intelligence, fn($t) => !empty($t['issues'])));
    }

    /**
     * AI/search context for why each check matters — used by intelligence output.
     */
    private function aiContext(string $key): string
    {
        return match ($key) {
            'title' => 'AI systems use the title to determine what a page covers. Without it, your page cannot be properly classified or recommended.',
            'meta_description' => 'AI platforms read meta descriptions to build content summaries. This is often what appears when AI cites your page.',
            'h1' => 'The primary heading anchors your content topic. AI uses this as the definitive statement of what the page is about.',
            'h2_sections' => 'Sub-headings create content depth. AI maps these to determine the scope and completeness of your coverage.',
            'word_count' => 'AI systems deprioritize thin content. More comprehensive pages are preferred as citation sources.',
            'json_ld' => 'Structured data is the most direct way to communicate with AI. Without it, AI must guess your business information.',
            'schema_type' => 'Entity classification tells AI your business type. This determines which query categories you appear in.',
            'entity_schema' => 'Industry-specific schema helps AI match you to relevant queries. Generic classification limits your visibility.',
            'rich_schema' => 'Multiple schema types create a rich knowledge graph entry. This is what separates cited businesses from ignored ones.',
            'business_name' => 'AI needs to identify your business by name to cite it. Without clear name signals, you cannot be referenced.',
            'services' => 'Service descriptions determine which queries AI considers you relevant for. Unclear offerings limit your query coverage.',
            'location' => 'Geographic signals determine local visibility. Without location context, you miss all location-qualified queries.',
            'link_count' => 'Internal links define your content graph. AI discovers pages through links — a weak graph means invisible content.',
            'faq' => 'FAQ content provides direct answers AI can extract and cite. This is the most extractable content format for AI.',
            'definitions' => 'Authoritative definitions position your site as a knowledge source. AI prefers citing sites that define their domain.',
            'structured_lists' => 'Structured lists provide scannable data AI can extract efficiently. Unstructured content requires more processing effort.',
            'https' => 'HTTPS is a baseline trust requirement. AI systems and search engines penalize insecure connections.',
            'response_time' => 'Slow sites get less AI crawl budget. Speed directly affects how much of your content gets indexed.',
            'indexable' => 'Blocked pages are completely invisible to AI. This is the most critical technical signal.',
            'canonical' => 'Without canonical URLs, AI may split your authority across duplicate versions of the same page.',
            default => 'This signal affects how AI systems discover, evaluate, and cite your content.',
        };
    }

    private function citationContext(string $key): string
    {
        return match ($key) {
            'json_ld' => 'Without structured data, AI must infer your business details from unstructured HTML — leading to inaccurate or missing citations.',
            'schema_type' => 'Entity type classification is how AI categorizes businesses. Unclassified entities are excluded from category-specific queries.',
            'entity_schema' => 'Industry classification connects you to the right query categories. Without it, AI cannot recommend you for industry-specific needs.',
            'rich_schema' => 'Multi-layer schema creates a rich knowledge graph entry — the level of detail that triggers featured citations in AI responses.',
            'business_name' => 'Your business name must be machine-readable for AI to cite you. Without clear name signals, AI cannot reference your business.',
            'services' => 'AI determines your relevance from service descriptions. Vague or missing service language means missed query matches.',
            'location' => 'Location signals connect your business to geographic queries. Without them, you are invisible for "near me" and local searches.',
            default => 'This signal directly affects whether AI systems can cite your business accurately.',
        };
    }

    private function extractionContext(string $key): string
    {
        return match ($key) {
            'title' => 'The title tag is the first content signal AI processes. It determines whether your page gets evaluated at all.',
            'meta_description' => 'Meta descriptions become AI summaries. When AI cites you, this is typically the text it uses.',
            'h1' => 'H1 is the definitive content anchor. AI uses it as the authoritative statement of page topic.',
            'h2_sections' => 'Sub-headings map content depth. AI checks H2s to determine whether your coverage is comprehensive enough to cite.',
            'word_count' => 'Content substance determines citation eligibility. Thin pages are passed over — AI needs enough material to extract meaningful content.',
            'faq' => 'FAQ content maps directly to AI question-answering. This is the single highest-impact extractable format.',
            'definitions' => 'Definitions make your site a reference source. AI systems prefer citing sites that authoritatively define their domain.',
            'structured_lists' => 'Lists and steps are the easiest content for AI to extract and reformat. They convert directly to AI-generated summaries.',
            default => 'This affects how efficiently AI can extract and repurpose your content.',
        };
    }

    private function technicalContext(string $key): string
    {
        return match ($key) {
            'https' => 'HTTPS is a hard requirement for many AI systems. Insecure sites may be excluded from AI training data entirely.',
            'response_time' => 'AI crawlers have time budgets. Slow pages consume more budget, reducing how much of your site gets processed.',
            'indexable' => 'A noindex directive blocks all AI discovery. This is the most critical technical issue — it makes you completely invisible.',
            'canonical' => 'Without canonical signals, AI may index the wrong version of your page or split authority across duplicates.',
            default => 'This technical signal affects AI system access to your content.',
        };
    }

    // =========================================================================
    // Output Variability — Phrasing Rotation
    // =========================================================================

    private static array $phraseVariants = [
        'title' => [
            'pass' => [
                'Primary topic signal detected — AI systems can identify this page.',
                'Topic signal present — AI platforms can recognize your page content.',
                'Page topic clearly established — AI can classify your content.',
                'Strong topic declaration found — machines can categorize this page.',
            ],
            'fail' => [
                'Primary topic signal missing — AI systems cannot determine what this page covers.',
                'No topic signal detected — AI platforms have no way to classify this page.',
                'Page topic undefined — AI has no basis to categorize your content.',
                'Topic declaration absent — machines skip pages they cannot classify.',
            ],
        ],
        'meta_description' => [
            'pass' => [
                'Page summary signal present — AI systems have context for this page.',
                'Summary signal detected — AI platforms can contextualize your content.',
                'Content summary available — AI has the context it needs to surface this page.',
                'Page context signal found — machines can evaluate your page relevance.',
            ],
            'fail' => [
                'No page summary signal — AI systems have no context for this page.',
                'Summary signal absent — AI platforms lack context to surface this page.',
                'Page context missing — AI cannot determine when to recommend this content.',
                'No content summary — machines have no basis to evaluate your page relevance.',
            ],
        ],
        'h1' => [
            'pass' => [
                'Primary content marker detected — clear topic established.',
                'Content marker found — your primary topic is clearly defined.',
                'Strong content anchor present — AI can identify the page focus.',
                'Page focus signal detected — machines can determine your core topic.',
            ],
            'fail' => [
                'No primary content marker — the page lacks a clear topic signal.',
                'Content marker missing — AI cannot identify your page\'s primary topic.',
                'Page focus undefined — AI has no anchor point for your content.',
                'No content anchor detected — machines cannot determine what this page is about.',
            ],
        ],
        'h2_sections' => [
            'pass' => [
                'Multiple topic depth markers detected — strong content structure.',
                'Content depth signals present — AI can map your topic coverage.',
                'Topic structure established — machines can parse your content hierarchy.',
                'Strong depth coverage — AI recognizes comprehensive topic treatment.',
            ],
            'fail' => [
                'Insufficient topic depth markers — AI cannot map your content structure.',
                'Content depth signals weak — machines see a flat, unstructured page.',
                'Topic structure missing — AI cannot determine the scope of your coverage.',
                'No depth hierarchy detected — your content appears shallow to AI systems.',
            ],
        ],
        'word_count' => [
            'pass' => [
                'Sufficient content substance — AI systems have enough material to analyze.',
                'Strong content depth — AI platforms can extract meaningful information.',
                'Content volume adequate — machines have sufficient material to process.',
                'Substantive content detected — AI can build a comprehensive understanding.',
            ],
            'fail' => [
                'Insufficient content substance — AI systems deprioritize thin pages.',
                'Content depth below threshold — AI platforms skip pages with limited material.',
                'Page lacks substance — machines cannot extract enough to justify citing you.',
                'Thin content detected — AI systems favor more comprehensive sources.',
            ],
        ],
        'json_ld' => [
            'pass' => [
                'Structured data layer detected — AI systems can parse your business information.',
                'Data layer present — machine-readable business information available.',
                'Machine-readable context found — AI can extract your business details directly.',
                'Business data layer active — platforms can parse your information automatically.',
            ],
            'fail' => [
                'No structured data layer — AI systems cannot identify your business type or services.',
                'Data layer missing — machines cannot parse your business information.',
                'Machine-readable context absent — AI must guess your business details.',
                'No business data layer — platforms cannot automatically identify what you offer.',
            ],
        ],
        'schema_type' => [
            'pass' => [
                'Entity type declaration found — your business type is machine-readable.',
                'Business classification detected — AI knows what category you belong to.',
                'Entity category established — machines can properly classify your business.',
                'Type declaration present — AI platforms can categorize your entity correctly.',
            ],
            'fail' => [
                'No entity type declaration — AI cannot classify your business.',
                'Business classification missing — machines cannot determine your category.',
                'Entity category undefined — AI must infer what type of business you are.',
                'No type declaration — platforms default to generic classification for your site.',
            ],
        ],
        'entity_schema' => [
            'pass' => [
                'Business category signal detected — AI can identify your industry.',
                'Industry classification present — machines can match you to relevant queries.',
                'Category signal found — AI platforms know your business vertical.',
                'Industry identifier active — your business sector is machine-readable.',
            ],
            'fail' => [
                'No business category signal — AI cannot determine your industry.',
                'Industry classification missing — machines cannot match you to relevant queries.',
                'Category signal absent — AI platforms cannot place you in a business vertical.',
                'No industry identifier — your business sector is invisible to machines.',
            ],
        ],
        'rich_schema' => [
            'pass' => [
                'Multiple data layers detected — strong machine-readable foundation.',
                'Rich data coverage present — AI has deep context for your business.',
                'Comprehensive data signals found — machines can build a detailed profile.',
                'Multi-layer data foundation — AI platforms have extensive business context.',
            ],
            'fail' => [
                'Limited data depth — expand with additional machine-readable layers.',
                'Single-layer data only — AI has a shallow view of your business.',
                'Data coverage incomplete — machines lack the depth to fully profile your business.',
                'Minimal data foundation — AI platforms need richer context to recommend you.',
            ],
        ],
        'business_name' => [
            'pass' => [
                'Brand identity signal detected — AI can reference your business by name.',
                'Business name recognized — machines can cite you directly.',
                'Brand signal present — AI platforms can attribute information to your business.',
                'Identity marker found — your business name is extractable by AI systems.',
            ],
            'fail' => [
                'Brand identity signal missing — AI may not recognize your business by name.',
                'Business name unclear — machines cannot confidently cite you.',
                'Brand signal absent — AI platforms cannot attribute content to your business.',
                'No identity marker — your business name is not extractable by AI systems.',
            ],
        ],
        'services' => [
            'pass' => [
                'Offering clarity detected — AI can describe what your business provides.',
                'Service descriptions found — machines can match you to relevant queries.',
                'Clear offering signals present — AI knows what services you provide.',
                'Service identification active — platforms can recommend you for specific needs.',
            ],
            'fail' => [
                'No offering clarity — AI cannot describe what your business provides.',
                'Service descriptions missing — machines cannot match you to relevant queries.',
                'Offering signals absent — AI doesn\'t know what you provide.',
                'No service identification — platforms cannot recommend you for specific needs.',
            ],
        ],
        'location' => [
            'pass' => [
                'Geographic context detected — AI can surface you for location-relevant queries.',
                'Location signals present — machines can associate you with a service area.',
                'Geographic authority established — AI platforms know where you operate.',
                'Area coverage identified — your service territory is machine-readable.',
            ],
            'fail' => [
                'No geographic context — AI cannot associate you with a service area.',
                'Location signals missing — machines cannot determine where you operate.',
                'Geographic authority absent — AI platforms don\'t know your service area.',
                'No area coverage — your service territory is invisible to AI systems.',
            ],
        ],
        'link_count' => [
            'pass' => [
                'Strong content graph — AI can traverse your site comprehensively.',
                'Robust site connectivity — machines can discover your full content.',
                'Content connections well-established — AI can map your site completely.',
                'Healthy content network — platforms can navigate between your pages.',
            ],
            'fail' => [
                'Sparse content graph — AI cannot fully map your site.',
                'Weak content connections — AI discovery of your pages is limited.',
                'Content network fragmented — machines miss most of your pages.',
                'Poor site connectivity — AI can only see a fraction of your content.',
            ],
        ],
        'faq' => [
            'pass' => [
                'Direct answer content detected — AI can extract citable responses from your pages.',
                'Answer-ready content found — AI platforms can cite your responses directly.',
                'Extractable answers present — machines can quote your content in responses.',
                'Citable content identified — AI systems can surface your answers to user questions.',
            ],
            'fail' => [
                'No direct answer content — AI has no citable responses to extract.',
                'Answer content missing — AI platforms cannot extract quotable information.',
                'No extractable answers — machines have nothing to quote from your site.',
                'Citable content absent — AI systems cannot find answers to surface from your pages.',
            ],
        ],
        'definitions' => [
            'pass' => [
                'Authoritative definitions detected — your site frames topics AI references.',
                'Definition content found — AI can use your framing as a reference source.',
                'Authority language present — machines recognize your expertise signals.',
                'Reference-quality definitions identified — AI can cite your explanations.',
            ],
            'fail' => [
                'No authoritative definitions — AI cannot cite your site as a reference source.',
                'Definition content missing — machines have no basis to reference your expertise.',
                'Authority language absent — AI cannot establish your knowledge authority.',
                'No reference-quality content — AI platforms prefer sites that define their domain.',
            ],
        ],
        'structured_lists' => [
            'pass' => [
                'Organized content blocks detected — AI can extract structured information.',
                'Structured information present — machines can parse your content efficiently.',
                'Clear content organization found — AI can identify specific items and details.',
                'Well-organized blocks identified — platforms can extract itemized information.',
            ],
            'fail' => [
                'No organized content blocks — structured information improves AI extractability.',
                'Content lacks structure — machines struggle to parse unorganized information.',
                'No clear content organization — AI cannot isolate specific items and details.',
                'Unstructured presentation — platforms cannot efficiently extract your information.',
            ],
        ],
        'https' => [
            'pass' => [
                'Secure connection verified — trust signal established.',
                'Connection security confirmed — AI trust requirements met.',
                'Encryption active — your site meets baseline trust standards.',
                'Secure protocol detected — machines trust your connection.',
            ],
            'fail' => [
                'No secure connection — trust signals reduced for AI and users.',
                'Connection security missing — AI trust requirements not met.',
                'No encryption detected — trust signals are critically low.',
                'Insecure protocol — machines deprioritize sites without connection security.',
            ],
        ],
        'response_time' => [
            'pass' => [
                'Fast access — AI systems can efficiently reach your content.',
                'Quick response detected — machines can process your site without delays.',
                'Access speed confirmed — AI platforms prioritize responsive sites.',
                'Responsive connection — your site meets AI accessibility standards.',
            ],
            'fail' => [
                'Slow access — AI systems may deprioritize your site.',
                'Response delays detected — machines may skip slow-loading content.',
                'Access speed below threshold — AI platforms favor faster sites.',
                'Sluggish connection — your site risks being deprioritized by AI crawlers.',
            ],
        ],
        'indexable' => [
            'pass' => [
                'Page is discoverable — no access restrictions detected.',
                'Discovery access confirmed — AI systems can reach this page.',
                'No restrictions found — machines are free to access your content.',
                'Full accessibility verified — AI platforms can discover and process this page.',
            ],
            'fail' => [
                'Page is blocked from discovery — AI systems will skip this page entirely.',
                'Discovery access restricted — machines cannot reach this content.',
                'Access restriction detected — AI platforms are blocked from this page.',
                'Page hidden from AI — your content is invisible to discovery systems.',
            ],
        ],
        'canonical' => [
            'pass' => [
                'Authority consolidation set — prevents duplicate content dilution.',
                'Content authority signal present — AI knows which version to prioritize.',
                'Page authority established — machines won\'t split your citation strength.',
                'Consolidation signal active — your page authority is properly concentrated.',
            ],
            'fail' => [
                'No authority consolidation — duplicate content may dilute your citation strength.',
                'Content authority signal missing — AI may split attention across page versions.',
                'Page authority unconsolidated — machines may dilute your ranking potential.',
                'No consolidation signal — your citation strength may be fragmented.',
            ],
        ],
    ];

    private function rotatePhrase(string $default, string $checkKey, int $seed, string $type): string
    {
        $variants = self::$phraseVariants[$checkKey][$type] ?? null;
        if (!$variants || count($variants) < 2) {
            return $default;
        }

        $index = abs($seed + crc32($checkKey)) % count($variants);
        return $variants[$index];
    }
}
