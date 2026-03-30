<?php

namespace App\Services;

/**
 * DomainEnrichmentService
 *
 * Provides lightweight domain validation and enrichment for user-submitted
 * website URLs (e.g. during onboarding at /setup).
 *
 * Current status: stubs only — all methods are no-ops returning safe defaults.
 *
 * Phase 2 implementation plan:
 * ─────────────────────────────────────────────────────────────────────────────
 * 1. normalize()   — strip scheme/trailing slashes, lowercase, strip www
 * 2. isReachable() — HEAD request with 3s timeout (via Guzzle/Http facade)
 *                    Returns bool; never blocks registration on failure.
 * 3. dnsLookup()   — resolve A/AAAA records via dns_get_record()
 *                    Returns array of IP addresses or empty array.
 * 4. enrichBasic() — combine normalize + dnsLookup into a summary DTO
 *                    Store result in user_profiles.domain_meta (JSON column, Phase 2).
 *
 * Security notes:
 * - Never follow redirects to internal/private IP ranges (SSRF protection).
 * - Validate URL format before any HTTP/DNS call.
 * - All enrichment is fire-and-forget (queued job) — never blocks the request.
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * Usage (Phase 2):
 *   $service = app(DomainEnrichmentService::class);
 *   $clean   = $service->normalize($websiteUrl);
 *   // dispatch(new EnrichDomainJob($userId, $clean));
 */
class DomainEnrichmentService
{
    /**
     * Normalize a raw user-supplied URL to a bare domain.
     *
     * Examples:
     *   'https://www.Example.com/path?q=1' → 'example.com'
     *   'example.com'                      → 'example.com'
     *
     * @TODO Phase 2: implement with parse_url + strtolower + ltrim www
     */
    public function normalize(string $url): string
    {
        if (blank($url)) {
            return '';
        }

        $host = parse_url(
            str_starts_with($url, 'http') ? $url : 'https://' . $url,
            PHP_URL_HOST
        ) ?? $url;

        return strtolower(preg_replace('/^www\./i', '', $host) ?? $host);
    }

    /**
     * Check whether a domain resolves via DNS.
     * Returns false on any failure — never throws.
     *
     * @TODO Phase 2: add dns_get_record() call; cache result for 1 hour.
     */
    public function hasValidDns(string $domain): bool
    {
        // Stub — always returns true to avoid blocking onboarding.
        // @TODO Phase 2: return (bool) checkdnsrr($domain, 'A');
        return true;
    }

    /**
     * Is the site reachable via HTTP HEAD?
     * Returns false on any network failure, timeout, or private-IP redirect.
     *
     * @TODO Phase 2: implement with Http::timeout(3)->head($url)
     *        + SSRF guard: reject responses that redirect to RFC-1918 ranges.
     */
    public function isReachable(string $url): bool
    {
        // Stub — always returns true.
        return true;
    }
}
