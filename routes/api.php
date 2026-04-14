<?php

use App\Http\Controllers\Api\V1\LicenseController;
use App\Http\Controllers\BookingWebhookController;
use App\Http\Controllers\QuickScanWebhookController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/validate', [LicenseController::class, 'validateLicense'])->middleware('throttle:api-public');
    Route::post('/stripe/webhook', [LicenseController::class, 'handleStripeWebhook']);

    // Booking payment webhook — confirms awaiting_payment bookings asynchronously.
    // Register in Stripe Dashboard: checkout.session.completed
    // Must be excluded from CSRF (API routes skip CSRF by default).
    Route::post('/book/stripe-webhook', [BookingWebhookController::class, 'handle']);

    // Quick Scan payment webhook — runs scan + email drip if user never returns.
    // Register in Stripe Dashboard: checkout.session.completed
    Route::post('/quick-scan/stripe-webhook', [QuickScanWebhookController::class, 'handle']);

    // Stripe checkout — public, called from WP plugin admin page.
    Route::post('/checkout', [LicenseController::class, 'createCheckoutSession'])->middleware('throttle:api-public');

    // Crypto checkout (Coinbase Commerce) — parallel path, same rules as Stripe checkout.
    Route::post('/crypto/checkout', [\App\Http\Controllers\Api\V1\CryptoCheckoutController::class, 'createCharge'])->middleware('throttle:api-public');
    Route::post('/crypto/webhook', [\App\Http\Controllers\Api\V1\CryptoCheckoutController::class, 'handleWebhook']);

    Route::middleware(['web', 'auth', EnsureUserIsAdmin::class])->group(function (): void {
        Route::post('/licenses', [LicenseController::class, 'store']);
        Route::get('/licenses/{key}', [LicenseController::class, 'show']);
    });
});