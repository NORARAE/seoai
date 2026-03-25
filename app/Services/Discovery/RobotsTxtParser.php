<?php

namespace App\Services\Discovery;

class RobotsTxtParser
{
    /**
     * Parse robots.txt content and return crawl policy arrays.
     *
     * @return array{allow_rules: array<int, string>, disallow_rules: array<int, string>, sitemap_urls: array<int, string>, crawl_delay: int}
     */
    public function parse(string $robotsTxt): array
    {
        $allowRules = [];
        $disallowRules = [];
        $sitemaps = [];
        $crawlDelay = 1;

        $activeUserAgent = false;

        foreach (preg_split('/\r\n|\r|\n/', $robotsTxt) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode(':', $line, 2), 2, null);
            $key = strtolower(trim((string) $key));
            $value = trim((string) $value);

            if ($key === 'user-agent') {
                $ua = strtolower($value);
                $activeUserAgent = in_array($ua, ['*', 'seoaico', 'seoaico/1.0 (seo crawler)'], true);
                continue;
            }

            if ($key === 'sitemap') {
                if (filled($value)) {
                    $sitemaps[] = $value;
                }

                continue;
            }

            if (! $activeUserAgent) {
                continue;
            }

            if ($key === 'allow' && $value !== '') {
                $allowRules[] = $value;
            }

            if ($key === 'disallow' && $value !== '') {
                $disallowRules[] = $value;
            }

            if ($key === 'crawl-delay' && is_numeric($value)) {
                $crawlDelay = max(1, (int) $value);
            }
        }

        return [
            'allow_rules' => array_values(array_unique($allowRules)),
            'disallow_rules' => array_values(array_unique($disallowRules)),
            'sitemap_urls' => array_values(array_unique($sitemaps)),
            'crawl_delay' => $crawlDelay,
        ];
    }

    public function isAllowed(string $path, array $allowRules, array $disallowRules): bool
    {
        $path = '/' . ltrim($path, '/');

        $bestMatchLength = -1;
        $bestMatchAllow = true;

        foreach ($disallowRules as $rule) {
            if ($this->ruleMatches($path, $rule) && strlen($rule) > $bestMatchLength) {
                $bestMatchLength = strlen($rule);
                $bestMatchAllow = false;
            }
        }

        foreach ($allowRules as $rule) {
            if ($this->ruleMatches($path, $rule) && strlen($rule) >= $bestMatchLength) {
                $bestMatchLength = strlen($rule);
                $bestMatchAllow = true;
            }
        }

        return $bestMatchAllow;
    }

    protected function ruleMatches(string $path, string $rule): bool
    {
        if ($rule === '') {
            return false;
        }

        $pattern = preg_quote($rule, '/');
        $pattern = str_replace('\\*', '.*', $pattern);
        $pattern = str_replace('\\$', '$', $pattern);

        return (bool) preg_match('/^' . $pattern . '/i', $path);
    }
}
