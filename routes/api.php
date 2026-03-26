<?php

use App\Http\Controllers\Api\V1\LicenseController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/validate', [LicenseController::class, 'validateLicense'])->middleware('throttle:api-public');
    Route::post('/stripe/webhook', [LicenseController::class, 'handleStripeWebhook']);

    // Checkout — public, called from WP plugin admin page.
    Route::post('/checkout', [LicenseController::class, 'createCheckoutSession'])->middleware('throttle:api-public');

    Route::middleware(['web', 'auth', EnsureUserIsAdmin::class])->group(function (): void {
        Route::post('/licenses', [LicenseController::class, 'store']);
        Route::get('/licenses/{key}', [LicenseController::class, 'show']);
    });
});