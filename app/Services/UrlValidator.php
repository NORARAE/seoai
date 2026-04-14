<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class UrlValidator
{
    private const BLOCKED_HOSTS = [
        'localhost',
        'localhost.localdomain',
        'broadcasthost',
    ];

    /**
     * Validate a URL is safe for scanning — blocks private IPs, reserved
     * ranges, localhost, missing TLD, and non-http(s) schemes.
     *
     * @return array{valid: bool, error: string|null}
     */
    public function validate(string $url): array
    {
        $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return ['valid' => false, 'error' => 'Only http and https URLs are supported.'];
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return ['valid' => false, 'error' => 'Enter a valid website address, such as yoursite.com'];
        }

        $hostLower = strtolower($host);

        if (in_array($hostLower, self::BLOCKED_HOSTS, true)) {
            return ['valid' => false, 'error' => 'Enter a public website address — internal or local URLs cannot be scanned.'];
        }

        // Require at least one dot (valid TLD)
        if (!str_contains($host, '.')) {
            return ['valid' => false, 'error' => 'Enter a valid domain name with a TLD, such as yoursite.com'];
        }

        // If host is a raw IP, validate it directly
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->checkPublicIp($host);
        }

        // DNS resolve → block private/reserved IPs (SSRF protection)
        $ips = @gethostbynamel($hostLower);
        if ($ips === false || empty($ips)) {
            return ['valid' => false, 'error' => 'This domain does not resolve — please check the URL and try again.'];
        }

        foreach ($ips as $ip) {
            $result = $this->checkPublicIp($ip);
            if (!$result['valid']) {
                return ['valid' => false, 'error' => 'Enter a public website address — internal or local URLs cannot be scanned.'];
            }
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Lightweight reachability check — HEAD then GET fallback, with http fallback.
     *
     * @return array{reachable: bool, error: string|null}
     */
    public function checkReachability(string $url): array
    {
        $ua = ['User-Agent' => 'SEOAIco-Scanner/1.0 (+https://seoaico.com)'];

        // Try HEAD first (fast)
        try {
            $response = Http::timeout(5)->withHeaders($ua)->head($url);
            if ($response->successful() || $response->status() < 500) {
                return ['reachable' => true, 'error' => null];
            }
        } catch (\Throwable) {
            // Fall through to GET
        }

        // Try GET
        try {
            $response = Http::timeout(5)->withHeaders($ua)->get($url);
            if ($response->successful()) {
                return ['reachable' => true, 'error' => null];
            }
            return ['reachable' => false, 'error' => 'We could not reach this website (HTTP ' . $response->status() . '). Please check the URL.'];
        } catch (\Throwable) {
            // Fall through to http fallback
        }

        // Fallback: try http:// if original was https://
        if (str_starts_with($url, 'https://')) {
            $httpUrl = 'http://' . substr($url, 8);
            try {
                $response = Http::timeout(5)->withHeaders($ua)->get($httpUrl);
                if ($response->successful()) {
                    return ['reachable' => true, 'error' => null];
                }
            } catch (\Throwable) {
                // Fall through
            }
        }

        return ['reachable' => false, 'error' => 'We could not reach this website. Please confirm it is publicly accessible and try again.'];
    }

    /**
     * Reject private, reserved, loopback, and link-local IP addresses.
     */
    private function checkPublicIp(string $ip): array
    {
        $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;

        if (!filter_var($ip, FILTER_VALIDATE_IP, $flags)) {
            return ['valid' => false, 'error' => 'Enter a public website address — internal or local URLs cannot be scanned.'];
        }

        return ['valid' => true, 'error' => null];
    }
}
