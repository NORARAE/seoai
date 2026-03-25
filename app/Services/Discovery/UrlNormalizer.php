<?php

namespace App\Services\Discovery;

class UrlNormalizer
{
    /**
     * Normalize a URL for deduplicated crawl indexing.
     *
     * @return array{normalized_url: string, path: string}
     */
    public function normalize(string $url): array
    {
        $url = trim($url);

        if (blank($url)) {
            return [
                'normalized_url' => '',
                'path' => '/',
            ];
        }

        $url = explode('#', $url)[0];

        $parsed = parse_url($url);

        if (! $parsed || ! isset($parsed['host'])) {
            return [
                'normalized_url' => '',
                'path' => '/',
            ];
        }

        $scheme = strtolower($parsed['scheme'] ?? 'https');
        $host = strtolower($parsed['host']);
        $path = '/' . ltrim($parsed['path'] ?? '/', '/');

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        $query = $this->normalizeQuery($parsed['query'] ?? null);

        $normalized = $scheme . '://' . $host . $path;

        if ($query !== '') {
            $normalized .= '?' . $query;
        }

        return [
            'normalized_url' => $normalized,
            'path' => $path,
        ];
    }

    /**
     * Determine if a URL is internal to a given domain.
     */
    public function isInternal(string $url, string $domain): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return false;
        }

        $host = strtolower(preg_replace('/^www\./i', '', $host));
        $domain = strtolower(preg_replace('/^www\./i', '', $domain));

        return $host === $domain;
    }

    /**
     * Resolve relative links against a base URL.
     */
    public function resolveUrl(string $href, string $baseUrl): ?string
    {
        $href = trim($href);

        if (blank($href) || str_starts_with($href, '#')) {
            return null;
        }

        if (preg_match('/^(javascript|mailto|tel|data|ftp):/i', $href)) {
            return null;
        }

        if (filter_var($href, FILTER_VALIDATE_URL)) {
            return $href;
        }

        $base = parse_url($baseUrl);

        if (! $base || ! isset($base['host'])) {
            return null;
        }

        $scheme = $base['scheme'] ?? 'https';
        $host = $base['host'];
        $port = isset($base['port']) ? ':' . $base['port'] : '';

        if (str_starts_with($href, '//')) {
            return $scheme . ':' . $href;
        }

        if (str_starts_with($href, '/')) {
            return $scheme . '://' . $host . $port . $href;
        }

        $basePath = $base['path'] ?? '/';
        $directory = rtrim(dirname($basePath), '/');

        return $scheme . '://' . $host . $port . $directory . '/' . ltrim($href, './');
    }

    protected function normalizeQuery(?string $query): string
    {
        if (blank($query)) {
            return '';
        }

        parse_str($query, $params);

        $blocked = [
            'fbclid',
            'gclid',
            'msclkid',
            'phpsessid',
            'session',
            'sessionid',
            'sid',
        ];

        foreach (array_keys($params) as $key) {
            $keyLower = strtolower($key);

            if (str_starts_with($keyLower, 'utm_') || in_array($keyLower, $blocked, true)) {
                unset($params[$key]);
            }
        }

        if (empty($params)) {
            return '';
        }

        ksort($params);

        return http_build_query($params);
    }
}
