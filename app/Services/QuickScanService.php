<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class QuickScanService
{
    /**
     * Run all checks on the given URL and return a structured result.
     *
     * @param  string  $url  The fully-qualified URL to scan
     * @return array{score:int, issues:array, strengths:array, fastest_fix:string, raw_checks:array, error:string|null}
     */
    public function scan(string $url): array
    {
        // Fetch the HTML
        $html = null;
        $fetchError = null;

        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'SEOAIco-Scanner/1.0 (+https://seoaico.com)'])
                ->get($url);

            if ($response->successful()) {
                $html = $response->body();
            } else {
                $fetchError = 'Could not fetch URL (HTTP ' . $response->status() . ')';
            }
        } catch (\Throwable $e) {
            $fetchError = 'Could not reach URL: ' . Str::limit($e->getMessage(), 120);
        }

        if ($html === null) {
            return [
                'score' => 0,
                'issues' => ['We could not fetch your website. Please check the URL and try again.'],
                'strengths' => [],
                'fastest_fix' => 'Ensure your website is publicly accessible and try again.',
                'raw_checks' => [],
                'error' => $fetchError,
            ];
        }

        // Run all checks
        $checks = [
            'schema_present' => $this->checkSchema($html),
            'faq_present' => $this->checkFaq($html),
            'definition_present' => $this->checkDefinitions($html),
            'internal_links' => $this->checkInternalLinks($html, $url),
            'content_length' => $this->checkContentLength($html),
        ];

        // Weight map (must sum to 100)
        $weights = [
            'schema_present' => 30,
            'faq_present' => 20,
            'definition_present' => 20,
            'internal_links' => 15,
            'content_length' => 15,
        ];

        // Score descriptions
        $checkMeta = [
            'schema_present' => [
                'label' => 'Schema / Structured Data',
                'pass' => 'Schema markup detected — AI systems can read your structured data.',
                'fail' => 'No schema markup found — AI systems cannot identify your business type or services.',
                'fix' => 'Add JSON-LD schema markup (Organization, LocalBusiness, or Service) to your homepage.',
            ],
            'faq_present' => [
                'label' => 'FAQ / Q&A Content',
                'pass' => 'FAQ or Q&A content found — AI can extract direct answers from your pages.',
                'fail' => 'No FAQ or Q&A content found — AI systems have nothing to cite as a direct answer.',
                'fix' => 'Add a FAQ section answering the top 5 questions your customers actually ask.',
            ],
            'definition_present' => [
                'label' => 'Definitions & Explanations',
                'pass' => 'Explanatory/definition content found — your site frames topics AI can reference.',
                'fail' => 'No clear definitions or explanations found — AI cannot use your site as an authoritative reference.',
                'fix' => 'Add at least one "What is…" or "How does…" section that clearly defines your core service.',
            ],
            'internal_links' => [
                'label' => 'Internal Link Structure',
                'pass' => 'Good internal link structure — AI can traverse your content graph.',
                'fail' => 'Weak internal link structure — AI systems cannot build a picture of your site authority.',
                'fix' => 'Link from your homepage to your core service and location pages.',
            ],
            'content_length' => [
                'label' => 'Content Depth',
                'pass' => 'Sufficient content depth — your pages give AI systems enough to work with.',
                'fail' => 'Thin content — pages with fewer than 300 words are rarely cited by AI systems.',
                'fix' => 'Expand your homepage content to at least 500 words covering your services and area.',
            ],
        ];

        // Build score
        $score = 0;
        $issues = [];
        $strengths = [];
        $firstFail = null;

        foreach ($checks as $key => $passed) {
            if ($passed) {
                $score += $weights[$key];
                $strengths[] = $checkMeta[$key]['pass'];
            } else {
                $issues[] = $checkMeta[$key]['fail'];
                if ($firstFail === null) {
                    $firstFail = $checkMeta[$key]['fix'];
                }
            }
        }

        $fastestFix = $firstFail ?? 'Your site is well-structured for AI citation. Consider expanding topic coverage.';

        return [
            'score' => $score,
            'issues' => $issues,
            'strengths' => $strengths,
            'fastest_fix' => $fastestFix,
            'raw_checks' => $checks,
            'error' => null,
        ];
    }

    // ── Individual check methods ──────────────────────────────────────────

    private function checkSchema(string $html): bool
    {
        // Look for JSON-LD or itemtype/microdata schema
        if (Str::contains($html, ['application/ld+json', '@type', 'schema.org', 'itemtype='])) {
            return true;
        }
        return false;
    }

    private function checkFaq(string $html): bool
    {
        $lower = strtolower($html);
        // FAQ heading patterns
        $patterns = ['faq', 'frequently asked', '<details', '<dl>', '<dt>', 'question', 'how do i', 'how does', 'what is'];
        foreach ($patterns as $p) {
            if (str_contains($lower, $p)) {
                return true;
            }
        }
        return false;
    }

    private function checkDefinitions(string $html): bool
    {
        $lower = strtolower($html);
        $patterns = ['what is ', 'what are ', 'how does ', 'how do ', 'defined as', 'refers to', 'is a service', 'is the process'];
        foreach ($patterns as $p) {
            if (str_contains($lower, $p)) {
                return true;
            }
        }
        return false;
    }

    private function checkInternalLinks(string $html, string $url): bool
    {
        $parsed = parse_url($url);
        $domain = $parsed['host'] ?? '';

        if (!$domain) {
            return false;
        }

        preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        $links = $matches[1] ?? [];

        $internalCount = 0;
        foreach ($links as $link) {
            $link = trim($link);
            if (empty($link) || $link === '#' || str_starts_with($link, 'javascript:') || str_starts_with($link, 'mailto:') || str_starts_with($link, 'tel:')) {
                continue;
            }
            // Relative links count as internal
            if (!str_starts_with($link, 'http')) {
                $internalCount++;
                continue;
            }
            // Absolute links to same domain
            $linkHost = parse_url($link, PHP_URL_HOST) ?? '';
            if ($linkHost && str_contains($linkHost, $domain)) {
                $internalCount++;
            }
        }

        return $internalCount >= 5;
    }

    private function checkContentLength(string $html): bool
    {
        // Strip HTML tags and count words
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        $words = str_word_count(trim($text));

        return $words >= 300;
    }
}
