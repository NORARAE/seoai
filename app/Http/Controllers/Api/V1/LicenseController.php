<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Licensing\LicenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
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