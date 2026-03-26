<?php

namespace App\Services\Licensing;

use App\Models\License;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use UnexpectedValueException;

class CryptoPaymentService
{
    private const BASE_URL    = 'https://api.commerce.coinbase.com';
    private const API_VERSION = '2018-03-22';

    /**
     * Create a Coinbase Commerce hosted charge and return its data array.
     * The caller receives ['id', 'hosted_url', 'code', …].
     *
     * @throws \RuntimeException  when the Coinbase Commerce API returns an error
     * @throws \InvalidArgumentException  when the plan/term combo is unknown
     */
    public function createCharge(array $data): array
    {
        if (! config('services.coinbase_commerce.enabled', false)) {
            throw new \RuntimeException('Crypto payments are not enabled.');
        }

        $plan      = $data['plan'];
        $term      = (int) $data['term_months'];
        $plans     = (array) config('license.plans', []);

        if (! isset($plans[$plan])) {
            throw new \InvalidArgumentException("Unknown plan: {$plan}");
        }

        // Total in cents → dollars string e.g. "891.00"
        $totalDollars = number_format(((int) $plans[$plan]['monthly_price']) * $term / 100, 2, '.', '');

        $metadata = [
            'plan'           => $plan,
            'term_months'    => (string) $term,
            'site_url'       => $data['site_url'],
            'customer_email' => $data['customer_email'],
            'customer_name'  => $data['customer_name'] ?? '',
        ];

        $response = Http::withHeaders([
            'X-CC-Api-Key'   => (string) config('services.coinbase_commerce.api_key'),
            'X-CC-Version'   => self::API_VERSION,
            'Content-Type'   => 'application/json',
        ])->post(self::BASE_URL . '/charges', [
            'name'         => 'SEOAIco License — ' . strtoupper($plan),
            'description'  => ucfirst($plan) . ' plan · ' . $term . '-month term',
            'local_price'  => [
                'amount'   => $totalDollars,
                'currency' => 'USD',
            ],
            'pricing_type' => 'fixed_price',
            'metadata'     => $metadata,
            'redirect_url' => url('/checkout/success'),
            'cancel_url'   => url('/checkout/cancelled'),
        ]);

        if (! $response->successful()) {
            Log::error('CoinbaseCommerce: charge creation failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'plan'   => $plan,
                'term'   => $term,
            ]);

            throw new \RuntimeException(
                'Crypto checkout is unavailable. Please try card payment or contact support.'
            );
        }

        return $response->json('data');
    }

    /**
     * Verify the Coinbase Commerce webhook signature and return the decoded event.
     *
     * Coinbase sends: X-CC-Webhook-Signature: <hex HMAC-SHA256 of raw body>
     *
     * @throws UnexpectedValueException  on missing secret, bad signature, or malformed payload
     */
    public function parseWebhook(string $payload, string $signature): array
    {
        $secret = (string) config('services.coinbase_commerce.webhook_secret');

        if ($secret === '') {
            throw new UnexpectedValueException('Coinbase Commerce webhook secret is not configured.');
        }

        $computed = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($computed, strtolower($signature))) {
            throw new UnexpectedValueException('Coinbase Commerce webhook signature mismatch.');
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            throw new UnexpectedValueException('Coinbase Commerce webhook payload is not valid JSON.');
        }

        return $event;
    }

    /**
     * Route the verified event to the appropriate handler.
     *
     * Webhook handling is always gated by the enabled flag so that stale
     * retries from Coinbase cannot activate licenses when the feature is off.
     *
     * @return array{handled: bool, event: string, charge_id: string|null, result: string}
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        // Hard gate — if feature is off, acknowledge receipt without processing
        if (! config('services.coinbase_commerce.enabled', false)) {
            Log::info('CoinbaseCommerce: webhook received but feature is disabled — acknowledged without processing');
            return ['handled' => false, 'event' => 'disabled', 'charge_id' => null, 'result' => 'feature_disabled'];
        }

        $event      = $this->parseWebhook($payload, $signature);
        $type       = $event['type'] ?? 'unknown';
        $chargeData = $event['data'] ?? [];
        $chargeId   = $chargeData['id'] ?? null;

        Log::info('CoinbaseCommerce: webhook received', [
            'type'      => $type,
            'charge_id' => $chargeId,
        ]);

        $action = $this->resolveAction($type);

        if ($action === null) {
            Log::info('CoinbaseCommerce: unhandled webhook event type (ignored)', [
                'type'      => $type,
                'charge_id' => $chargeId,
            ]);
            return ['handled' => false, 'event' => $type, 'charge_id' => $chargeId, 'result' => 'unhandled_type'];
        }

        if (! $chargeId) {
            Log::warning('CoinbaseCommerce: webhook missing charge ID', ['type' => $type, 'payload_keys' => array_keys($chargeData)]);
            return ['handled' => false, 'event' => $type, 'charge_id' => null, 'result' => 'missing_charge_id'];
        }

        // Explicit guard: failed/expired events must NEVER activate a license
        if ($action === 'expire') {
            $this->expireByCharge($chargeId, $type);
            Log::info('CoinbaseCommerce: webhook processed', [
                'type'      => $type,
                'charge_id' => $chargeId,
                'action'    => 'expire',
            ]);
            return ['handled' => true, 'event' => $type, 'charge_id' => $chargeId, 'result' => 'expired'];
        }

        if ($action === 'activate') {
            $this->activateLicense($chargeData, $chargeData['metadata'] ?? []);
            Log::info('CoinbaseCommerce: webhook processed', [
                'type'      => $type,
                'charge_id' => $chargeId,
                'action'    => 'activate',
            ]);
            return ['handled' => true, 'event' => $type, 'charge_id' => $chargeId, 'result' => 'activated'];
        }

        return ['handled' => false, 'event' => $type, 'charge_id' => $chargeId, 'result' => 'no_action'];
    }

    // ──────────────────────────────────────────────────

    private function resolveAction(string $eventType): ?string
    {
        return match ($eventType) {
            'charge:confirmed', 'charge:resolved' => 'activate',
            'charge:failed', 'charge:expired'     => 'expire',
            default                               => null,
        };
    }

    private function activateLicense(array $chargeData, array $metadata): void
    {
        $chargeId      = $chargeData['id'];
        $plan          = $metadata['plan'] ?? null;
        $termMonths    = isset($metadata['term_months']) ? (int) $metadata['term_months'] : null;
        $siteUrl       = $metadata['site_url'] ?? null;
        $customerEmail = $metadata['customer_email'] ?? null;
        $customerName  = $metadata['customer_name'] ?? '';

        if (! $plan || ! $siteUrl || ! $customerEmail) {
            Log::warning('CoinbaseCommerce: missing metadata on confirmed charge', [
                'charge_id' => $chargeId,
                'metadata'  => $metadata,
            ]);
            return;
        }

        // Idempotency — do not create a second license for the same charge
        if (License::where('crypto_charge_id', $chargeId)->exists()) {
            Log::info('CoinbaseCommerce: license already exists for charge (duplicate webhook)', [
                'charge_id' => $chargeId,
            ]);
            return;
        }

        $licenseService = app(LicenseService::class);

        try {
            $license = $licenseService->createLicense([
                'plan'             => $plan,
                'term_months'      => $termMonths,
                'site_url'         => $siteUrl,
                'customer_email'   => $customerEmail,
                'customer_name'    => $customerName,
                'status'           => 'active',
                'payment_method'   => 'crypto',
                'crypto_charge_id' => $chargeId,
            ]);

            Log::info('CoinbaseCommerce: license created from confirmed payment', [
                'charge_id'   => $chargeId,
                'plan'        => $plan,
                'license_key' => $license->license_key,
            ]);
        } catch (\DomainException $e) {
            // Domain already has a license; reactivate it rather than blocking
            $normalizedSite = $licenseService->normalizeDomain($siteUrl);

            $existing = License::where('site_url', $normalizedSite)
                ->whereIn('status', ['trial', 'expired'])
                ->first();

            if ($existing) {
                $existing->forceFill([
                    'status'           => 'active',
                    'plan'             => $plan,
                    'payment_method'   => 'crypto',
                    'crypto_charge_id' => $chargeId,
                    'expires_at'       => $termMonths ? now()->addMonths($termMonths) : null,
                ])->save();

                Log::info('CoinbaseCommerce: existing license reactivated via crypto payment', [
                    'charge_id'   => $chargeId,
                    'license_key' => $existing->license_key,
                ]);
            } else {
                Log::error('CoinbaseCommerce: could not create or reactivate license', [
                    'charge_id' => $chargeId,
                    'error'     => $e->getMessage(),
                ]);
            }
        }
    }

    private function expireByCharge(string $chargeId, string $eventType): void
    {
        $license = License::where('crypto_charge_id', $chargeId)->first();

        if ($license) {
            $license->forceFill(['status' => 'expired'])->save();

            Log::info('CoinbaseCommerce: license expired due to charge failure', [
                'charge_id'   => $chargeId,
                'event'       => $eventType,
                'license_key' => $license->license_key,
            ]);
        }
    }
}
