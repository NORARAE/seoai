<?php

namespace App\Services\Licensing;

use App\Actions\Licensing\GenerateLicenseKey;
use App\Mail\LicenseIssued;
use App\Models\License;
use App\Models\LicenseValidation;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Stripe\Webhook;
use UnexpectedValueException;

class LicenseService
{
    public function __construct(
        protected GenerateLicenseKey $generateLicenseKey,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function validateLicense(string $licenseKey, string $siteUrl, ?string $pluginVersion = null, ?string $ipAddress = null): array
    {
        $normalizedKey = Str::upper(trim($licenseKey));
        $normalizedSite = $this->normalizeDomain($siteUrl);

        $license = License::query()->where('license_key', $normalizedKey)->first();

        if (! $license) {
            $this->logValidation($normalizedKey, $normalizedSite ?? trim($siteUrl), $pluginVersion, 'invalid', $ipAddress);

            return [
                'valid' => false,
                'reason' => 'invalid_key',
            ];
        }

        $expectedSite = $this->normalizeDomain($license->site_url);

        if (! $normalizedSite || ! $expectedSite || $normalizedSite !== $expectedSite) {
            $this->logValidation($license->license_key, $normalizedSite ?? trim($siteUrl), $pluginVersion, 'invalid', $ipAddress);

            return [
                'valid' => false,
                'reason' => 'domain_mismatch',
            ];
        }

        if (in_array($license->status, ['cancelled', 'expired'], true)) {
            $this->logValidation($license->license_key, $normalizedSite, $pluginVersion, 'invalid', $ipAddress);

            return [
                'valid' => false,
                'reason' => 'expired',
            ];
        }

        if ($license->status === 'trial' && $license->trial_ends_at && $license->trial_ends_at->isPast()) {
            $license->forceFill([
                'status' => 'expired',
                'expires_at' => $license->trial_ends_at,
            ])->save();

            $this->logValidation($license->license_key, $normalizedSite, $pluginVersion, 'invalid', $ipAddress);

            return [
                'valid' => false,
                'reason' => 'trial_expired',
            ];
        }

        if ($license->expires_at && $license->expires_at->isPast()) {
            $license->forceFill([
                'status' => 'expired',
            ])->save();

            $this->logValidation($license->license_key, $normalizedSite, $pluginVersion, 'invalid', $ipAddress);

            return [
                'valid' => false,
                'reason' => 'expired',
            ];
        }

        $this->logValidation($license->license_key, $normalizedSite, $pluginVersion, 'valid', $ipAddress);

        return [
            'valid' => true,
            'plan' => $license->plan,
            'urls_allowed' => $license->urls_allowed,
            'expires_at' => $license->expires_at?->toDateString() ?? $license->trial_ends_at?->toDateString(),
        ];
    }

    public function createLicense(array $attributes, bool $sendEmail = true): License
    {
        return DB::transaction(function () use ($attributes, $sendEmail): License {
            $plan = $attributes['plan'];
            $status = $attributes['status'] ?? 'trial';
            $termMonths = isset($attributes['term_months']) ? (int) $attributes['term_months'] : null;
            $siteUrl = $this->normalizeDomain((string) $attributes['site_url']);

            // Enforce minimum term when a paid term is specified.
            $minTerm = (int) config('license.min_term_months', 3);
            if ($termMonths !== null && $termMonths < $minTerm) {
                $termMonths = $minTerm;
            }

            // Block duplicate: one active/trial license per domain.
            $existing = License::query()
                ->where('site_url', $siteUrl)
                ->whereIn('status', ['trial', 'active'])
                ->first();

            if ($existing) {
                throw new \DomainException(
                    "An active license already exists for {$siteUrl} (key: {$existing->license_key}). "
                    . 'Renew or upgrade the existing license instead of creating a duplicate.',
                );
            }

            $license = License::query()->create([
                'license_key' => ($this->generateLicenseKey)(),
                'customer_email' => Str::lower(trim((string) $attributes['customer_email'])),
                'customer_name' => trim((string) $attributes['customer_name']),
                'site_url' => $siteUrl,
                'plan' => $plan,
                'urls_allowed' => $attributes['urls_allowed'] ?? $this->urlsAllowedForPlan($plan),
                'stripe_subscription_id' => $attributes['stripe_subscription_id'] ?? null,
                'stripe_customer_id' => $attributes['stripe_customer_id'] ?? null,
                'status' => $status,
                'trial_ends_at' => $status === 'trial'
                    ? CarbonImmutable::parse($attributes['trial_ends_at'] ?? now()->addDays((int) config('license.trial_days', 30)))
                    : (! empty($attributes['trial_ends_at']) ? CarbonImmutable::parse($attributes['trial_ends_at']) : null),
                'expires_at' => $attributes['expires_at']
                    ?? ($termMonths ? now()->addMonths($termMonths) : null),
            ]);

            if ($sendEmail && $license->customer_email) {
                Mail::to($license->customer_email)->send(new LicenseIssued($license));
            }

            return $license;
        });
    }

    public function findLicense(string $licenseKey): ?License
    {
        return License::query()
            ->with(['validations' => fn ($query) => $query->latest('created_at')->limit(20)])
            ->where('license_key', Str::upper(trim($licenseKey)))
            ->first();
    }

    /**
     * @return array{handled: bool, event: string, license_key: string|null}
     */
    public function handleStripeWebhook(string $payload, ?string $signatureHeader): array
    {
        $secret = (string) config('cashier.webhook.secret');

        if ($secret === '') {
            throw new UnexpectedValueException('Stripe webhook secret is not configured.');
        }

        $event = Webhook::constructEvent(
            $payload,
            (string) $signatureHeader,
            $secret,
            (int) config('cashier.webhook.tolerance', 300),
        );

        $type = $event->type;
        $data = $event->data->object;
        $license = null;

        if ($type === 'checkout.session.completed' && ($data->mode ?? null) === 'subscription' && ! empty($data->subscription)) {
            $subscription = Cashier::stripe()->subscriptions->retrieve((string) $data->subscription, []);

            $license = $this->syncLicenseFromStripeSubscription($subscription, [
                'site_url' => Arr::get((array) ($data->metadata ?? []), 'site_url'),
                'plan' => Arr::get((array) ($data->metadata ?? []), 'plan'),
                'customer_email' => Arr::get((array) ($data->customer_details ?? []), 'email'),
                'customer_name' => Arr::get((array) ($data->customer_details ?? []), 'name'),
            ]);
        }

        if (in_array($type, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted'], true)) {
            $license = $this->syncLicenseFromStripeSubscription($data, [], $type === 'customer.subscription.deleted');
        }

        return [
            'handled' => true,
            'event' => $type,
            'license_key' => $license?->license_key,
        ];
    }

    public function normalizeDomain(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $candidate = trim(Str::lower($url));

        if ($candidate === '') {
            return null;
        }

        if (! str_contains($candidate, '://')) {
            $candidate = 'https://'.$candidate;
        }

        $host = parse_url($candidate, PHP_URL_HOST);

        if (! is_string($host) || trim($host) === '') {
            return null;
        }

        return preg_replace('/^www\./', '', trim(Str::lower($host))) ?: null;
    }

    protected function logValidation(string $licenseKey, ?string $siteUrl, ?string $pluginVersion, string $result, ?string $ipAddress): void
    {
        LicenseValidation::query()->create([
            'license_key' => Str::upper(trim($licenseKey)),
            'site_url' => $siteUrl,
            'plugin_ver' => $pluginVersion,
            'result' => $result,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }

    protected function urlsAllowedForPlan(string $plan): ?int
    {
        return config("license.plans.{$plan}.urls_allowed");
    }

    protected function syncLicenseFromStripeSubscription(object $subscription, array $context = [], bool $forceCancelled = false): ?License
    {
        $subscriptionId = (string) ($subscription->id ?? '');
        $customerId = (string) ($subscription->customer ?? '');
        $subscriptionMetadata = (array) ($subscription->metadata ?? []);

        $customer = null;

        if ($customerId !== '') {
            try {
                $customer = Cashier::stripe()->customers->retrieve($customerId, []);
            } catch (\Throwable $exception) {
                Log::warning('Unable to retrieve Stripe customer during license sync.', [
                    'customer_id' => $customerId,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $customerMetadata = (array) (($customer?->metadata) ?? []);
        $siteUrl = $this->normalizeDomain(
            $context['site_url']
            ?? Arr::get($subscriptionMetadata, 'site_url')
            ?? Arr::get($customerMetadata, 'site_url')
            ?? null
        );

        $plan = $this->resolvePlan(
            $context['plan']
            ?? Arr::get($subscriptionMetadata, 'plan')
            ?? Arr::get($customerMetadata, 'plan')
            ?? null,
            (string) Arr::get((array) ($subscription->items->data[0]->price ?? null), 'id', ''),
        );

        $license = License::query()
            ->when($subscriptionId !== '', fn ($query) => $query->orWhere('stripe_subscription_id', $subscriptionId))
            ->when($customerId !== '', fn ($query) => $query->orWhere('stripe_customer_id', $customerId))
            ->when($siteUrl !== null, fn ($query) => $query->orWhere('site_url', $siteUrl))
            ->first();

        if (! $license && ! $siteUrl) {
            Log::warning('Skipping license sync because no site_url metadata was available.', [
                'stripe_subscription_id' => $subscriptionId,
                'stripe_customer_id' => $customerId,
            ]);

            return null;
        }

        $status = $forceCancelled
            ? 'cancelled'
            : $this->mapStripeStatusToLicenseStatus((string) ($subscription->status ?? 'active'));

        $license ??= new License();

        $isNew = ! $license->exists;

        $license->fill([
            'license_key' => $license->license_key ?: ($this->generateLicenseKey)(),
            'customer_email' => $context['customer_email']
                ?? $customer?->email
                ?? $license->customer_email,
            'customer_name' => $context['customer_name']
                ?? $customer?->name
                ?? $license->customer_name,
            'site_url' => $siteUrl ?? $license->site_url,
            'plan' => $plan ?? $license->plan ?? 'starter',
            'urls_allowed' => $plan ? $this->urlsAllowedForPlan($plan) : $license->urls_allowed,
            'stripe_subscription_id' => $subscriptionId ?: $license->stripe_subscription_id,
            'stripe_customer_id' => $customerId ?: $license->stripe_customer_id,
            'status' => $status,
            'trial_ends_at' => ! empty($subscription->trial_end)
                ? CarbonImmutable::createFromTimestamp((int) $subscription->trial_end)
                : null,
            'expires_at' => ! empty($subscription->current_period_end)
                ? CarbonImmutable::createFromTimestamp((int) $subscription->current_period_end)
                : ($forceCancelled ? now() : $license->expires_at),
        ]);

        $license->save();

        if ($isNew && $license->customer_email) {
            Mail::to($license->customer_email)->send(new LicenseIssued($license));
        }

        return $license;
    }

    protected function resolvePlan(?string $plan, ?string $stripePriceId): ?string
    {
        if ($plan && config("license.plans.{$plan}")) {
            return $plan;
        }

        if (! $stripePriceId) {
            return null;
        }

        foreach ((array) config('license.plans', []) as $slug => $definition) {
            // Legacy single price_id
            if (($definition['stripe_price_id'] ?? null) === $stripePriceId) {
                return $slug;
            }
            // Term-based prices map  {3 => 'price_xxx', 6 => 'price_yyy', …}
            foreach (($definition['stripe_prices'] ?? []) as $priceId) {
                if ($priceId === $stripePriceId) {
                    return $slug;
                }
            }
        }

        return null;
    }

    protected function mapStripeStatusToLicenseStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'trialing' => 'trial',
            'canceled', 'incomplete_expired' => 'cancelled',
            default => 'active',
        };
    }
}