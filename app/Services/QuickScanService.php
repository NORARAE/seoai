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

        foreach ($categories as $cat) {
            foreach ($cat['checks'] as $check) {
                if ($check['passed']) {
                    $strengths[] = $check['pass'];
                } else {
                    $issues[] = $check['fail'];
                    if ($firstFix === null) {
                        $firstFix = $check['fix'];
                    }
                }
            }
        }

        // 404 detection on internal links
        $linkAudit = $this->auditInternalLinks($html, $url);

        if (!empty($linkAudit['broken'])) {
            $count = count($linkAudit['broken']);
            $issues[] = "{$count} broken internal link(s) detected — these return errors and hurt crawlability.";
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
            'fastest_fix' => $firstFix ?? 'Your site is well-structured for AI citation. Consider expanding topic coverage.',
            'raw_checks' => $rawChecks,
            'broken_links' => $linkAudit['broken'],
            'page_count' => $linkAudit['total'],
            'error' => null,
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
                'score' => 0, 'categories' => [],
                'issues' => ['This URL resolves to a private or reserved network address and cannot be scanned.'],
                'strengths' => [], 'fastest_fix' => 'Ensure your website is hosted on a public IP address.',
                'raw_checks' => [], 'broken_links' => [], 'page_count' => 0,
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
            'key' => 'title', 'passed' => $hasTitle, 'points' => $hasTitle ? 4 : 0, 'max' => 4,
            'label' => 'Page Title',
            'pass' => 'Page has a title tag — AI systems can identify the page topic.',
            'fail' => 'No title tag found — AI systems cannot determine what this page is about.',
            'fix' => 'Add a descriptive <title> tag that includes your primary service and location.',
        ];

        // Meta description (4 pts)
        $hasDesc = (bool) preg_match('/<meta\s[^>]*name=["\']description["\'][^>]*content=["\'].+["\']/is', $html)
                || (bool) preg_match('/<meta\s[^>]*content=["\'].+["\']\s[^>]*name=["\']description["\']/is', $html);
        $checks[] = [
            'key' => 'meta_description', 'passed' => $hasDesc, 'points' => $hasDesc ? 4 : 0, 'max' => 4,
            'label' => 'Meta Description',
            'pass' => 'Meta description present — search engines and AI have a summary to work with.',
            'fail' => 'No meta description — AI systems have no summary of your page purpose.',
            'fix' => 'Add a meta description tag (150-160 chars) summarizing your core offering.',
        ];

        // H1 tag (4 pts)
        $hasH1 = (bool) preg_match('/<h1[\s>]/i', $html);
        $checks[] = [
            'key' => 'h1', 'passed' => $hasH1, 'points' => $hasH1 ? 4 : 0, 'max' => 4,
            'label' => 'Primary Heading (H1)',
            'pass' => 'H1 heading found — clear page topic established.',
            'fail' => 'No H1 heading — the page lacks a clear primary topic signal.',
            'fix' => 'Add a single H1 heading that states your primary service or business name.',
        ];

        // H2 sections (4 pts for 2+)
        preg_match_all('/<h2[\s>]/i', $html, $h2s);
        $h2Count = count($h2s[0] ?? []);
        $hasH2s = $h2Count >= 2;
        $checks[] = [
            'key' => 'h2_sections', 'passed' => $hasH2s, 'points' => $hasH2s ? 4 : 0, 'max' => 4,
            'label' => 'Content Sections (H2)',
            'pass' => "Multiple content sections ({$h2Count} headings) — good topic structure.",
            'fail' => 'Fewer than 2 content sections — AI cannot map your topic depth.',
            'fix' => 'Break content into 2-3 H2 sections covering different aspects of your service.',
        ];

        // Word count (4 pts for 300+)
        $text = preg_replace('/\s+/', ' ', strip_tags($html));
        $wordCount = str_word_count(trim($text));
        $hasWords = $wordCount >= 300;
        $checks[] = [
            'key' => 'word_count', 'passed' => $hasWords, 'points' => $hasWords ? 4 : 0, 'max' => 4,
            'label' => 'Content Depth',
            'pass' => "Sufficient content ({$wordCount} words) — AI systems have enough to work with.",
            'fail' => "Thin content ({$wordCount} words) — pages under 300 words are rarely cited by AI.",
            'fix' => 'Expand page content to at least 300-500 words covering services and area.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 20, 'label' => 'Content Coverage', 'checks' => $checks];
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
            'key' => 'json_ld', 'passed' => $hasJsonLd, 'points' => $hasJsonLd ? 10 : 0, 'max' => 10,
            'label' => 'JSON-LD Markup',
            'pass' => 'JSON-LD structured data found — AI systems can parse your business data.',
            'fail' => 'No JSON-LD markup — AI systems cannot identify your business type or services.',
            'fix' => 'Add JSON-LD schema markup (Organization, LocalBusiness, or Service) to your page.',
        ];

        // Has @type (5 pts)
        $hasType = $hasJsonLd && str_contains($html, '"@type"');
        $checks[] = [
            'key' => 'schema_type', 'passed' => $hasType, 'points' => $hasType ? 5 : 0, 'max' => 5,
            'label' => 'Schema Type',
            'pass' => 'Schema type declared — your entity type is machine-readable.',
            'fail' => 'No schema type found — AI cannot classify your business.',
            'fix' => 'Include @type in your JSON-LD (e.g., "LocalBusiness", "Organization", "Service").',
        ];

        // Business entity type (5 pts)
        $entityTypes = ['Organization', 'LocalBusiness', 'ProfessionalService', 'MedicalBusiness',
            'LegalService', 'FinancialService', 'RealEstateAgent', 'HomeAndConstructionBusiness',
            'Store', 'Restaurant', 'AutoRepair', 'Service'];
        $hasEntity = false;
        foreach ($entityTypes as $type) {
            if (str_contains($html, $type)) {
                $hasEntity = true;
                break;
            }
        }
        $checks[] = [
            'key' => 'entity_schema', 'passed' => $hasEntity, 'points' => $hasEntity ? 5 : 0, 'max' => 5,
            'label' => 'Business Entity Schema',
            'pass' => 'Business entity type found in schema — AI can identify your business category.',
            'fail' => 'No business entity schema — AI cannot determine your business type.',
            'fix' => 'Use a specific business @type like LocalBusiness, ProfessionalService, etc.',
        ];

        // Rich schema (multiple types or microdata) (5 pts)
        preg_match_all('/"@type"\s*:\s*"([^"]+)"/', $html, $typeMatches);
        $typeCount = count(array_unique($typeMatches[1] ?? []));
        $hasMicrodata = (bool) preg_match('/itemtype=["\']https?:\/\/schema\.org\//i', $html);
        $richSchema = $typeCount >= 2 || ($hasJsonLd && $hasMicrodata);
        $checks[] = [
            'key' => 'rich_schema', 'passed' => $richSchema, 'points' => $richSchema ? 5 : 0, 'max' => 5,
            'label' => 'Rich Schema Coverage',
            'pass' => 'Multiple schema types or rich markup — strong structured data foundation.',
            'fail' => 'Limited schema depth — expand with additional @types for services, reviews, FAQ.',
            'fix' => 'Add FAQPage, Service, or Review schema alongside your primary business schema.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 25, 'label' => 'Schema & Structured Data', 'checks' => $checks];
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
            'key' => 'business_name', 'passed' => $hasName, 'points' => $hasName ? 5 : 0, 'max' => 5,
            'label' => 'Business Identity',
            'pass' => 'Business name identifiable — AI can reference you by name.',
            'fail' => 'Business name not clearly identifiable — AI may not know what to call you.',
            'fix' => 'Ensure your business name appears in the H1, title tag, and schema markup.',
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
            'key' => 'services', 'passed' => $hasServices, 'points' => $hasServices ? 5 : 0, 'max' => 5,
            'label' => 'Service Descriptions',
            'pass' => 'Service or offering language detected — AI can describe what you do.',
            'fail' => 'No clear service descriptions — AI cannot explain your offering to users.',
            'fix' => 'Add clear descriptions of your services or products using natural language.',
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
            'key' => 'location', 'passed' => $hasLocation, 'points' => $hasLocation ? 5 : 0, 'max' => 5,
            'label' => 'Location / Service Area',
            'pass' => 'Geographic context found — AI can recommend you for local queries.',
            'fail' => 'No location or service area mentioned — AI cannot match you to local searches.',
            'fix' => 'Mention your city, service area, or address on the page and in schema.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 15, 'label' => 'Entity Clarity', 'checks' => $checks];
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
        $checks = [[
            'key' => 'link_count', 'passed' => $passed, 'points' => $points, 'max' => 15,
            'label' => 'Internal Link Count',
            'pass' => "Strong internal linking ({$uniqueCount} unique links) — AI can traverse your content.",
            'fail' => $uniqueCount === 0
                ? 'No internal links found — AI cannot discover your other pages.'
                : "Weak internal linking ({$uniqueCount} unique links) — AI cannot fully map your site.",
            'fix' => 'Link from your homepage to your core service, location, and FAQ pages.',
        ]];

        return ['score' => $points, 'max' => 15, 'label' => 'Internal Linking', 'checks' => $checks];
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
            'key' => 'faq', 'passed' => $hasFaq, 'points' => $hasFaq ? 5 : 0, 'max' => 5,
            'label' => 'FAQ / Q&A Content',
            'pass' => 'FAQ or Q&A content found — AI can extract direct answers from your pages.',
            'fail' => 'No FAQ or Q&A content — AI systems have nothing to cite as a direct answer.',
            'fix' => 'Add a FAQ section answering the top 5 questions your customers actually ask.',
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
            'key' => 'definitions', 'passed' => $hasDefs, 'points' => $hasDefs ? 5 : 0, 'max' => 5,
            'label' => 'Definitions & Explanations',
            'pass' => 'Explanatory content found — your site frames topics AI can reference.',
            'fail' => 'No clear definitions — AI cannot use your site as an authoritative reference.',
            'fix' => 'Add a "What is…" or "How does…" section defining your core service.',
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
            'key' => 'structured_lists', 'passed' => $hasLists, 'points' => $hasLists ? 5 : 0, 'max' => 5,
            'label' => 'Structured Lists',
            'pass' => 'Structured list content found — AI can extract itemized information.',
            'fail' => 'No structured lists — bulleted or numbered lists improve AI extractability.',
            'fix' => 'Add lists of services, benefits, or steps using <ul> or <ol> with 3+ items.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 15, 'label' => 'Extractable Content', 'checks' => $checks];
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
            'key' => 'https', 'passed' => $isHttps, 'points' => $isHttps ? 3 : 0, 'max' => 3,
            'label' => 'HTTPS',
            'pass' => 'Site uses HTTPS — secure connection verified.',
            'fail' => 'Site not using HTTPS — reduces trust signals for both AI and users.',
            'fix' => 'Enable SSL/HTTPS on your website.',
        ];

        // Response time < 3s (3 pts)
        $fast = $responseTime < 3.0;
        $checks[] = [
            'key' => 'response_time', 'passed' => $fast, 'points' => $fast ? 3 : 0, 'max' => 3,
            'label' => 'Response Speed',
            'pass' => "Fast response ({$responseTime}s) — AI crawlers can efficiently access your content.",
            'fail' => "Slow response ({$responseTime}s) — AI crawlers may time out or deprioritize your site.",
            'fix' => 'Improve server response time to under 3 seconds.',
        ];

        // Not noindex (2 pts)
        $hasNoindex = (bool) preg_match('/<meta[^>]*name=["\']robots["\'][^>]*content=["\'][^"\']*noindex/i', $html);
        $indexable = !$hasNoindex;
        $checks[] = [
            'key' => 'indexable', 'passed' => $indexable, 'points' => $indexable ? 2 : 0, 'max' => 2,
            'label' => 'Indexability',
            'pass' => 'Page is indexable — no noindex directive found.',
            'fail' => 'Page has a noindex tag — AI systems will skip this page entirely.',
            'fix' => 'Remove the noindex meta tag to allow AI systems to crawl and cite your content.',
        ];

        // Canonical URL (2 pts)
        $hasCanonical = (bool) preg_match('/<link[^>]*rel=["\']canonical["\']/i', $html);
        $checks[] = [
            'key' => 'canonical', 'passed' => $hasCanonical, 'points' => $hasCanonical ? 2 : 0, 'max' => 2,
            'label' => 'Canonical URL',
            'pass' => 'Canonical URL set — prevents duplicate content confusion.',
            'fail' => 'No canonical URL — duplicate content may dilute your AI citation authority.',
            'fix' => 'Add a <link rel="canonical"> tag pointing to the preferred URL of this page.',
        ];

        $total = collect($checks)->sum('points');
        return ['score' => $total, 'max' => 10, 'label' => 'Crawlability', 'checks' => $checks];
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
}
