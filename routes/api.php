<?php

use App\Http\Controllers\Api\V1\LicenseController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/validate', [LicenseController::class, 'validateLicense']);
    Route::post('/stripe/webhook', [LicenseController::class, 'handleStripeWebhook']);

    Route::middleware(['web', 'auth', EnsureUserIsAdmin::class])->group(function (): void {
        Route::post('/licenses', [LicenseController::class, 'store']);
        Route::get('/licenses/{key}', [LicenseController::class, 'show']);
    });
});