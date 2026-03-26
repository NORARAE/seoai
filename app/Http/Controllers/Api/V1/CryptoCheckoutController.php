<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Licensing\CryptoPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class CryptoCheckoutController extends Controller
{
    public function __construct(
        protected CryptoPaymentService $cryptoPaymentService,
    ) {
    }

    /**
     * Create a Coinbase Commerce hosted charge.
     * Called from the WP plugin the same way POST /api/v1/checkout is called.
     *
     * Returns: { "checkout_url": "https://commerce.coinbase.com/charges/XXXX" }
     */
    public function createCharge(Request $request): JsonResponse
    {
        if (! config('services.coinbase_commerce.enabled', false)) {
            return response()->json([
                'message' => 'Crypto payments are not currently available.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $planKeys = array_keys((array) config('license.plans', []));
        $terms    = array_keys((array) config('license.terms', []));

        $data = $request->validate([
            'plan'           => ['required', 'string', Rule::in($planKeys)],
            'term_months'    => ['required', 'integer', Rule::in($terms)],
            'site_url'       => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_name'  => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $charge = $this->cryptoPaymentService->createCharge($data);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json([
            'checkout_url' => $charge['hosted_url'],
            'charge_id'    => $charge['id'],
            'charge_code'  => $charge['code'],
        ]);
    }

    /**
     * Receive and process Coinbase Commerce webhooks.
     * Coinbase signs each request with X-CC-Webhook-Signature (HMAC-SHA256).
     * Must use raw body — do NOT let Laravel parse it first.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $signature = (string) $request->header('X-CC-Webhook-Signature', '');

        try {
            $result = $this->cryptoPaymentService->handleWebhook(
                $request->getContent(),
                $signature,
            );
        } catch (UnexpectedValueException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($result);
    }
}
