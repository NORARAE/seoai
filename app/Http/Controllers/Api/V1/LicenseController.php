<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Licensing\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Cashier;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService,
    ) {
    }

    public function validateLicense(Request $request): JsonResponse
    {
        $data = $request->validate([
            'license_key' => ['required', 'string', 'max:64'],
            'site_url' => ['required', 'string', 'max:255'],
            'plugin_ver' => ['nullable', 'string', 'max:50'],
        ]);

        $result = $this->licenseService->validateLicense(
            $data['license_key'],
            $data['site_url'],
            Arr::get($data, 'plugin_ver'),
            $request->ip(),
        );

        return response()->json($result);
    }

    public function store(Request $request): JsonResponse
    {
        $planKeys = array_keys((array) config('license.plans', []));

        $data = $request->validate([
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'site_url' => ['required', 'string', 'max:255'],
            'plan' => ['required', 'string', Rule::in($planKeys)],
            'urls_allowed' => ['nullable', 'integer', 'min:1'],
            'term_months' => ['nullable', 'integer', 'min:' . config('license.min_term_months', 3)],
            'stripe_subscription_id' => ['nullable', 'string', 'max:255'],
            'stripe_customer_id' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['trial', 'active', 'expired', 'cancelled'])],
            'trial_ends_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        try {
            $license = $this->licenseService->createLicense($data, (bool) ($data['send_email'] ?? true));
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return response()->json([
            'data' => $license,
        ], Response::HTTP_CREATED);
    }

    public function show(string $key): JsonResponse
    {
        $license = $this->licenseService->findLicense($key);

        abort_if(! $license, Response::HTTP_NOT_FOUND, 'License not found.');

        return response()->json([
            'data' => $license,
        ]);
    }

    public function createCheckoutSession(Request $request): JsonResponse|RedirectResponse
    {
        $plans = (array) config('license.plans', []);
        $terms = array_keys((array) config('license.terms', []));

        $data = $request->validate([
            'plan' => ['required', 'string', Rule::in(array_keys($plans))],
            'term_months' => ['required', 'integer', Rule::in($terms)],
            'site_url' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
        ]);

        $plan = $data['plan'];
        $term = (int) $data['term_months'];
        $priceId = $plans[$plan]['stripe_prices'][$term] ?? null;

        if (! $priceId) {
            return response()->json([
                'message' => "No Stripe Price ID configured for {$plan} / {$term}-month term. Contact support.",
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $siteUrl = $this->licenseService->normalizeDomain($data['site_url']) ?? $data['site_url'];

        $session = Cashier::stripe()->checkout->sessions->create([
            'mode' => 'subscription',
            'customer_email' => $data['customer_email'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'metadata' => [
                'site_url' => $siteUrl,
                'plan' => $plan,
                'term_months' => $term,
            ],
            'subscription_data' => [
                'metadata' => [
                    'site_url' => $siteUrl,
                    'plan' => $plan,
                    'term_months' => $term,
                ],
            ],
            'success_url' => $request->input('success_url', url('/checkout/success')) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $request->input('cancel_url', url('/checkout/cancelled')),
        ]);

        return response()->json(['checkout_url' => $session->url]);
    }

    public function handleStripeWebhook(Request $request): JsonResponse
    {
        try {
            $result = $this->licenseService->handleStripeWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature'),
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($result);
    }
}