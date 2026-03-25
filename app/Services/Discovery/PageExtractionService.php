<?php

namespace App\Services\Discovery;

use Symfony\Component\DomCrawler\Crawler;

class PageExtractionService
{
    protected int $maxHtmlBytes = 2_500_000;

    protected int $maxBodyTextChars = 250_000;

    /**
     * @return array{
     *   title: ?string,
     *   meta_description: ?string,
     *   canonical: ?string,
     *   h1: ?string,
     *   h2s: array<int, string>,
     *   meta_robots: ?string,
     *   schema: array<int, mixed>,
     *   body_text: string,
     *   excerpt: string,
     *   word_count: int,
     *   readability: float,
     *   links: array<int, array{url: string, anchor_text: ?string, rel: ?string}>,
     * }
     */
    public function extract(string $html, string $url): array
    {
        $html = $this->truncateUtf8ByBytes($html, $this->maxHtmlBytes);

        $crawler = new Crawler($html, $url);

        $title = $this->firstText($crawler, 'title');
        $metaDescription = $this->metaContent($crawler, 'description');
        $canonical = $crawler->filter('link[rel="canonical"]')->count() > 0
            ? trim((string) $crawler->filter('link[rel="canonical"]')->first()->attr('href'))
            : null;
        $h1 = $this->firstText($crawler, 'h1');
        $h2s = $crawler->filter('h2')->each(fn (Crawler $node) => trim($node->text()));
        $metaRobots = $this->metaContent($crawler, 'robots');
        $schema = $this->extractSchema($crawler);

        $bodyText = trim($crawler->filter('body')->count() ? strip_tags($crawler->filter('body')->html() ?? '') : '');
        $bodyText = preg_replace('/\s+/', ' ', $bodyText) ?? $bodyText;
        $bodyText = mb_substr($bodyText, 0, $this->maxBodyTextChars);
        $excerpt = trim(substr($bodyText, 0, 260));
        $wordCount = str_word_count($bodyText);

        return [
            'title' => $title,
            'meta_description' => $metaDescription,
            'canonical' => $canonical,
            'h1' => $h1,
            'h2s' => array_values(array_filter($h2s)),
            'meta_robots' => $metaRobots,
            'schema' => $schema,
            'body_text' => $bodyText,
            'excerpt' => $excerpt,
            'word_count' => $wordCount,
            'readability' => $this->estimateReadability($bodyText),
            'links' => $this->extractLinks($crawler),
        ];
    }

    protected function firstText(Crawler $crawler, string $selector): ?string
    {
        if (! $crawler->filter($selector)->count()) {
            return null;
        }

        $text = trim((string) $crawler->filter($selector)->first()->text(''));

        return $text !== '' ? $text : null;
    }

    protected function metaContent(Crawler $crawler, string $name): ?string
    {
        $selector = 'meta[name="' . $name . '"]';

        if (! $crawler->filter($selector)->count()) {
            return null;
        }

        $content = trim((string) $crawler->filter($selector)->first()->attr('content'));

        return $content !== '' ? $content : null;
    }

    protected function extractSchema(Crawler $crawler): array
    {
        $schemas = [];

        $crawler->filter('script[type="application/ld+json"]')->each(function (Crawler $node) use (&$schemas) {
            $raw = trim((string) $node->text(''));

            if ($raw === '') {
                return;
            }

            $decoded = json_decode($raw, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $schemas[] = $decoded;
            }
        });

        return $schemas;
    }

    protected function extractLinks(Crawler $crawler): array
    {
        $links = [];

        $crawler->filter('a[href]')->each(function (Crawler $node) use (&$links): void {
            $href = trim((string) $node->attr('href'));

            if ($href === '') {
                return;
            }

            $links[] = [
                'url' => $href,
                'anchor_text' => trim((string) $node->text('')) ?: null,
                'rel' => $node->attr('rel'),
            ];
        });

        return $links;
    }

    protected function estimateReadability(string $text): float
    {
        $sentences = max(1, preg_match_all('/[.!?]+/', $text));
        $words = max(1, str_word_count($text));
        $syllables = $this->countSyllables($text);

        $score = 206.835 - (1.015 * ($words / $sentences)) - (84.6 * ($syllables / $words));

        return round(max(0, min(100, $score)), 2);
    }

    protected function countSyllables(string $text): int
    {
        $words = preg_split('/\s+/', strtolower($text)) ?: [];
        $count = 0;

        foreach ($words as $word) {
            $word = preg_replace('/[^a-z]/', '', $word) ?? '';

            if ($word === '') {
                continue;
            }

            preg_match_all('/[aeiouy]+/', $word, $matches);
            $count += max(1, count($matches[0]));
        }

        return max(1, $count);
    }

    protected function truncateUtf8ByBytes(string $value, int $maxBytes): string
    {
        if (strlen($value) <= $maxBytes) {
            return $value;
        }

        return mb_strcut($value, 0, $maxBytes, 'UTF-8');
    }
}
