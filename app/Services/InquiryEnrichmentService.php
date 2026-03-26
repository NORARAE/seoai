<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * InquiryEnrichmentService
 *
 * Centralises all outbound enrichment API calls made during inquiry submission.
 * CRITICAL CONTRACT: every public method must be fault-tolerant — a failed
 * external call must NEVER propagate an exception outward and must NEVER
 * prevent the inquiry from being saved to the database.
 *
 * Callers should call enrichAll() and pass the returned array to the inquiry
 * model. All partial failures are logged internally and result in null values.
 */
class InquiryEnrichmentService
{
    // Disposable email provider domains (local fallback list)
    private const DISPOSABLE_DOMAINS = [
        'mailinator.com', 'guerrillamail.com', 'guerrillamail.net', 'guerrillamail.org',
        'guerrillamail.biz', 'guerrillamail.de', 'guerrillamail.info', 'guerrillamail.us',
        'tempmail.com', 'throwaway.email', 'trashmail.com', 'trashmail.me', 'trashmail.at',
        'fakeinbox.com', 'sharklasers.com', 'guerrillamailblock.com', 'grr.la',
        'spam4.me', 'yopmail.com', 'yopmail.fr', 'cool.fr.nf', 'jetable.fr.nf',
        'nospam.ze.tc', 'nomail.xl.cx', 'mega.zik.dj', 'speed.1s.fr',
        'courriel.fr.nf', 'moncourrier.fr.nf', 'monemail.fr.nf',
        'spamgourmet.com', 'dispostable.com', 'mailnull.com', 'spamspot.com',
        'mailnesia.com', 'mailnull.com', 'maildrop.cc', 'spamgourmet.net',
        'discard.email', 'spamevader.com', 'throwam.com', '10minutemail.com',
        '10minutemail.net', 'tempr.email', 'discard.email', 'tempail.com',
        'filzmail.com', 'spamfree24.org', 'spamfree24.de', 'spamfree24.info',
        'spamfree24.biz', 'spamfree24.eu', 'spamfree.eu', 'spam.la',
        'getairmail.com', 'spamherelots.com', 'anonaddy.com', 'mailsac.com',
        'dispostable.com', 'getnada.com', 'inboxbear.com', 'throwem.com',
        'fakemailgenerator.com', 'tempinbox.com', 'spamgap.com',
    ];

    // Common free / consumer email providers
    private const FREE_DOMAINS = [
        'gmail.com', 'googlemail.com', 'yahoo.com', 'yahoo.co.uk', 'yahoo.co.in',
        'yahoo.ca', 'yahoo.com.au', 'yahoo.es', 'yahoo.fr', 'yahoo.de',
        'hotmail.com', 'hotmail.co.uk', 'hotmail.fr', 'hotmail.de', 'hotmail.it',
        'hotmail.es', 'live.com', 'live.co.uk', 'live.fr', 'live.de',
        'outlook.com', 'outlook.co.uk', 'msn.com', 'aol.com',
        'icloud.com', 'me.com', 'mac.com',
        'protonmail.com', 'protonmail.ch', 'pm.me',
        'zoho.com', 'fastmail.com', 'fastmail.fm',
        'rocketmail.com', 'ymail.com',
    ];

    /**
     * Run all enrichment checks and return a flat array of attributes
     * ready to be merged into the Inquiry::create() call.
     */
    public function enrichAll(string $ip, string $email, ?string $websiteUrl, ?int $formLoadedAt): array
    {
        $geo      = $this->lookupIp($ip);
        $urlCheck = $websiteUrl ? $this->checkUrl($websiteUrl) : [];
        $emailInfo = $this->classifyEmail($email);
        $company  = $websiteUrl ? $this->enrichCompany($websiteUrl) : [];
        $captcha  = null; // reCAPTCHA handled in controller (needs raw token)
        $timeToSubmit = $this->calcTimeToSubmit($formLoadedAt);

        return array_merge($geo, $urlCheck, $emailInfo, $company, [
            'time_to_submit_seconds' => $timeToSubmit,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // IP GEOLOCATION
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Look up IP geolocation via ip-api.com (free tier, no key required).
     * Returns attribute-keyed array safe to merge into inquiry data.
     */
    public function lookupIp(string $ip): array
    {
        // Skip private / loopback IPs
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return $this->emptyGeoData();
        }

        try {
            $response = Http::timeout(4)->get(
                "http://ip-api.com/json/{$ip}",
                ['fields' => 'status,country,regionName,city,isp,org,proxy,hosting,message']
            );

            if (! $response->successful()) {
                Log::warning('InquiryEnrichment: ip-api non-success', ['status' => $response->status(), 'ip' => $ip]);
                return $this->emptyGeoData();
            }

            $data = $response->json();

            if (($data['status'] ?? '') !== 'success') {
                return $this->emptyGeoData();
            }

            return [
                'ip_city'       => $this->truncate($data['city'] ?? null, 120),
                'ip_region'     => $this->truncate($data['regionName'] ?? null, 120),
                'ip_country'    => $this->truncate($data['country'] ?? null, 120),
                'ip_isp'        => $this->truncate(($data['org'] ?? $data['isp'] ?? null), 255),
                'ip_is_proxy'   => (bool) ($data['proxy'] ?? false),
                'ip_is_hosting' => (bool) ($data['hosting'] ?? false),
            ];
        } catch (\Throwable $e) {
            Log::warning('InquiryEnrichment: ip lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return $this->emptyGeoData();
        }
    }

    private function emptyGeoData(): array
    {
        return [
            'ip_city'       => null,
            'ip_region'     => null,
            'ip_country'    => null,
            'ip_isp'        => null,
            'ip_is_proxy'   => false,
            'ip_is_hosting' => false,
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // URL / WEBSITE VALIDATION
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Check whether the submitted URL resolves, returns an accessible page,
     * uses HTTPS, and shows no parked/placeholder signals.
     */
    public function checkUrl(string $rawUrl): array
    {
        $empty = ['url_status' => null, 'url_is_https' => false, 'domain_age_days' => null];

        $parsed = parse_url($rawUrl);
        if (empty($parsed['host'])) {
            return array_merge($empty, ['url_status' => 'unresolvable']);
        }

        $host = strtolower($parsed['host']);

        // DNS check
        try {
            $resolved = checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || checkdnsrr($host, 'CNAME');
            if (! $resolved) {
                return array_merge($empty, ['url_status' => 'unresolvable', 'url_is_https' => false]);
            }
        } catch (\Throwable $e) {
            Log::warning('InquiryEnrichment: DNS check failed', ['host' => $host, 'error' => $e->getMessage()]);
            return array_merge($empty, ['url_status' => null]);
        }

        // HTTP request — follow redirects, note final URL
        try {
            $response = Http::timeout(8)
                ->withOptions(['allow_redirects' => ['max' => 5, 'track_redirects' => true]])
                ->get($rawUrl);

            $statusCode  = $response->status();
            $isHttps     = str_starts_with(strtolower($rawUrl), 'https://');
            $body        = mb_strtolower(mb_substr($response->body(), 0, 8000));
            $finalUrl    = $response->effectiveUri()?->__toString() ?? $rawUrl;
            $finalHost   = strtolower(parse_url($finalUrl, PHP_URL_HOST) ?? $host);
            $redirected  = $finalHost !== $host;

            // Parked / placeholder signals
            $parkedSignals = ['buy this domain', 'this domain is for sale', 'domain for sale',
                              'coming soon', 'under construction', 'parked', 'parkingcrew',
                              'sedo.com', 'sedoparking', 'afternic', 'hugedomains'];
            $isParked = false;
            foreach ($parkedSignals as $signal) {
                if (str_contains($body, $signal)) {
                    $isParked = true;
                    break;
                }
            }

            if ($statusCode >= 200 && $statusCode < 400) {
                $urlStatus = $isParked ? 'parked' : ($redirected ? 'redirect' : 'valid');
            } else {
                $urlStatus = 'suspicious';
            }

            return [
                'url_status'      => $urlStatus,
                'url_is_https'    => $isHttps,
                'domain_age_days' => null,  // WHOIS requires an external lib; left null
            ];
        } catch (\Throwable $e) {
            Log::warning('InquiryEnrichment: URL check failed', ['url' => $rawUrl, 'error' => $e->getMessage()]);
            return array_merge($empty, ['url_status' => null]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // EMAIL CLASSIFICATION
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Classify an email as disposable | free | business.
     * First checks Kickbox open API (no key), falls back to local lists.
     */
    public function classifyEmail(string $email): array
    {
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        if (! $domain) {
            return ['email_type' => null];
        }

        // Local disposable check first (fast, no network)
        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            return ['email_type' => 'disposable'];
        }

        // Local free-provider check
        if (in_array($domain, self::FREE_DOMAINS, true)) {
            return ['email_type' => 'free'];
        }

        // Kickbox open API (no key required, rate-limited at 1200/hr per IP)
        try {
            $response = Http::timeout(4)->get(
                "https://open.kickbox.com/v1/disposable/{$domain}"
            );

            if ($response->successful()) {
                $disposable = $response->json('disposable');
                if ($disposable === true) {
                    return ['email_type' => 'disposable'];
                }
            }
        } catch (\Throwable $e) {
            Log::info('InquiryEnrichment: Kickbox check failed (non-fatal)', [
                'domain' => $domain,
                'error'  => $e->getMessage(),
            ]);
        }

        return ['email_type' => 'business'];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // COMPANY ENRICHMENT
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Attempt to enrich company data via Clearbit's free autocomplete endpoint.
     * No API key required — rate limited by Clearbit at ~1200/hr.
     * Stores a sanitised subset of the response; raw blobs are trimmed.
     */
    public function enrichCompany(string $websiteUrl): array
    {
        $domain = strtolower(parse_url($websiteUrl, PHP_URL_HOST) ?? '');
        // Strip leading www
        $domain = preg_replace('/^www\./', '', $domain) ?? $domain;

        if (! $domain) {
            return ['company_enrichment' => null];
        }

        try {
            $response = Http::timeout(5)->get(
                "https://autocomplete.clearbit.com/v1/companies/suggest",
                ['query' => $domain]
            );

            if (! $response->successful()) {
                return ['company_enrichment' => null];
            }

            $results = $response->json();
            if (! is_array($results) || empty($results)) {
                return ['company_enrichment' => null];
            }

            // Take the best match — first result
            $raw = $results[0];

            // Sanitise: store only known useful fields, not arbitrary blob
            $enrichment = array_filter([
                'name'        => $this->truncate($raw['name'] ?? null, 200),
                'domain'      => $this->truncate($raw['domain'] ?? null, 255),
                'logo'        => $this->truncate($raw['logo'] ?? null, 500),
                'industry'    => $this->truncate($raw['category']['industry'] ?? null, 120),
                'sub_industry'=> $this->truncate($raw['category']['subIndustry'] ?? null, 120),
                'employees'   => $raw['metrics']['employees'] ?? null,
                'founded'     => $raw['foundedYear'] ?? null,
                'linkedin'    => $this->truncate($raw['linkedin']['handle'] ?? null, 255),
                'location'    => $this->truncate($raw['location'] ?? null, 255),
            ], fn ($v) => $v !== null && $v !== '');

            return ['company_enrichment' => empty($enrichment) ? null : $enrichment];
        } catch (\Throwable $e) {
            Log::info('InquiryEnrichment: Clearbit enrichment failed (non-fatal)', [
                'domain' => $domain,
                'error'  => $e->getMessage(),
            ]);
            return ['company_enrichment' => null];
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RECAPTCHA V3
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Verify a reCAPTCHA v3 token and return the score (0.0–1.0) or null on failure.
     * Failure is non-fatal — caller must degrade gracefully.
     */
    public function verifyRecaptcha(string $token, string $ip): ?float
    {
        $secret = (string) config('services.recaptcha.secret_key', '');

        if ($secret === '') {
            return null;
        }

        try {
            $response = Http::asForm()->timeout(5)->post(
                'https://www.google.com/recaptcha/api/siteverify',
                ['secret' => $secret, 'response' => $token, 'remoteip' => $ip]
            );

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (! ($data['success'] ?? false)) {
                Log::info('InquiryEnrichment: reCAPTCHA verification failed', [
                    'errors' => $data['error-codes'] ?? [],
                ]);
                return null;
            }

            return isset($data['score']) ? (float) $data['score'] : null;
        } catch (\Throwable $e) {
            Log::warning('InquiryEnrichment: reCAPTCHA call failed (non-fatal)', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SPAM RISK SCORING
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Calculate a spam risk level from enrichment signals.
     * Returns ['spam_risk' => 'low'|'medium'|'high', '_score' => int, '_signals' => array].
     * The _score and _signals keys are prefixed with _ and should NOT be stored on the model;
     * they are used by the controller to build the spam_log entry.
     */
    public function scoreSpamRisk(array $enrichment): array
    {
        $score   = 0;
        $signals = [];

        $add = function (int $pts, string $reason) use (&$score, &$signals): void {
            $score  += $pts;
            $signals[] = $reason;
        };

        if (($enrichment['ip_is_proxy'] ?? false)) {
            $add(3, 'ip_proxy');
        }
        if (($enrichment['ip_is_hosting'] ?? false)) {
            $add(3, 'ip_hosting');
        }
        if (($enrichment['email_type'] ?? null) === 'disposable') {
            $add(3, 'email_disposable');
        }
        if (($enrichment['email_type'] ?? null) === 'free') {
            $add(1, 'email_free');
        }
        $urlStatus = $enrichment['url_status'] ?? null;
        if ($urlStatus !== null && $urlStatus !== 'valid') {
            $add(2, "url_status:{$urlStatus}");
        }
        $domainAge = $enrichment['domain_age_days'] ?? null;
        if ($domainAge !== null && $domainAge < 180) {
            $add(2, 'domain_age_lt_180');
        }
        $timeToSubmit = $enrichment['time_to_submit_seconds'] ?? null;
        if ($timeToSubmit !== null && $timeToSubmit < 4) {
            $add(3, 'fast_submit');
        }
        if (($enrichment['honeypot_triggered'] ?? false)) {
            $add(5, 'honeypot');
        }
        $recaptchaScore = $enrichment['recaptcha_score'] ?? null;
        if ($recaptchaScore !== null && $recaptchaScore < 0.5) {
            $add(3, 'low_recaptcha');
        }

        $risk = $score >= 6 ? 'high' : ($score >= 3 ? 'medium' : 'low');

        return [
            'spam_risk' => $risk,
            '_score'    => $score,
            '_signals'  => $signals,
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function calcTimeToSubmit(?int $formLoadedAtTimestamp): ?int
    {
        if (! $formLoadedAtTimestamp) {
            return null;
        }
        $elapsed = time() - $formLoadedAtTimestamp;
        // Sanity bounds: negative or absurdly large means tampered/unreliable
        if ($elapsed < 0 || $elapsed > 86400) {
            return null;
        }
        return $elapsed;
    }

    private function truncate(?string $value, int $max): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        return mb_strlen($value) > $max ? mb_substr($value, 0, $max) : $value;
    }
}
