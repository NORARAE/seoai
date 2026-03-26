<?php

use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationPagePreviewController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicSitemapController;
use App\Http\Controllers\SeoController;
use App\Http\Middleware\EnsureUserIsApproved;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SEOAI Application Routes
|--------------------------------------------------------------------------
|
| Three-layer architecture:
| 1. Public Layer (/) - Marketing & product info
| 2. Customer App Layer (/dashboard, /sites, /pages, etc.) - Main SaaS app
| 3. Internal Admin Layer (/admin) - Filament admin panel (see FilamentServiceProvider)
|
*/

// ============================================================================
// PUBLIC LAYER - Marketing & Landing Pages
// ============================================================================

Route::get('/', [PublicController::class, 'landing'])->name('home');

// Auth middleware redirects here when unauthenticated; forward to Filament login.
Route::get('/login', fn () => redirect('/admin/login'))->name('login');
Route::get('/privacy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicController::class, 'terms'])->name('terms');
Route::post('/licensing-inquiry', [PublicController::class, 'storeLicensingInquiry'])
    ->name('licensing-inquiry.store');
Route::get('/licensing-inquiry', fn () => redirect(url('/').'#contact'))->name('licensing-inquiry.get');

Route::get('/checkout/success', fn () => view('public.checkout-success'))->name('checkout.success');
Route::get('/checkout/cancelled', fn () => view('public.checkout-cancelled'))->name('checkout.cancelled');

// ── Booking / Consult System ──
Route::get('/book', [BookingController::class, 'index'])->name('book.index');
Route::get('/book/slots', [BookingController::class, 'getSlots'])->name('book.slots');
Route::post('/book', [BookingController::class, 'store'])->name('book.store');
Route::get('/book/confirm/{booking}', [BookingController::class, 'confirm'])->name('book.confirm');
Route::get('/book/cancel/{booking}', [BookingController::class, 'cancel'])->name('book.cancel');
Route::post('/book/cancel/{booking}', [BookingController::class, 'processCancel'])->name('book.processCancel');

Route::get('/sitemaps/{site}.xml', [PublicSitemapController::class, 'index'])
    ->whereNumber('site')
    ->name('public.sitemaps.index');
Route::get('/sitemaps/{site}/pages-{page}.xml', [PublicSitemapController::class, 'page'])
    ->whereNumber('site')
    ->whereNumber('page')
    ->name('public.sitemaps.page');

// ============================================================================
// CUSTOMER APP LAYER - Main SaaS Application
// ============================================================================

// Authenticated-but-unapproved users land here. No approval check to avoid loops.
Route::middleware('auth')->get('/pending-approval', fn () => view('pending-approval'))
    ->name('pending-approval');

Route::middleware(['auth', EnsureUserIsApproved::class])->prefix('dashboard')->name('app.')->group(function () {
    
    // Dashboard / Home
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Sites Management (future)
    // Route::get('/sites', [SiteController::class, 'index'])->name('sites.index');
    // Route::get('/sites/{site}', [SiteController::class, 'show'])->name('sites.show');
    
    // Pages Management (future - different from /admin/location-pages)
    // Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
    // Route::get('/pages/{page}', [PageController::class, 'show'])->name('pages.show');
    
    // Internal Links (future)
    // Route::get('/internal-links', [InternalLinkController::class, 'index'])->name('internal-links.index');
    
    // Reports (future)
    // Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    // Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    
    // Automations (future)
    // Route::get('/automations', [AutomationController::class, 'index'])->name('automations.index');
    
    // Sitemaps (future)
    // Route::get('/sitemaps', [SitemapController::class, 'index'])->name('sitemaps.index');
    
    // Schema Management (future)
    // Route::get('/schema', [SchemaController::class, 'index'])->name('schema.index');
    
    // Settings (future)
    // Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
});

// Convenience routes (redirect to app layer)
Route::get('/sites', fn() => redirect('/dashboard'))->name('sites.index');
Route::get('/pages', fn() => redirect('/dashboard'))->name('pages.index');
Route::get('/internal-links', fn() => redirect('/dashboard'))->name('internal-links.index');
Route::get('/reports', fn() => redirect('/dashboard'))->name('reports.index');
Route::get('/automations', fn() => redirect('/dashboard'))->name('automations.index');
Route::get('/sitemaps', fn() => redirect('/dashboard'))->name('sitemaps.index');
Route::get('/schema', fn() => redirect('/dashboard'))->name('schema.index');
Route::get('/settings', fn() => redirect('/dashboard'))->name('settings.index');

// ============================================================================
// ADMIN BOOKING MANAGEMENT (auth-protected)
// ============================================================================

Route::middleware(['auth', EnsureUserIsApproved::class])->prefix('admin/bookings')->name('admin.bookings.')->group(function () {
    Route::get('/', [AdminBookingController::class, 'index'])->name('index');
    Route::get('/availability', [AdminBookingController::class, 'availability'])->name('availability');
    Route::post('/availability', [AdminBookingController::class, 'saveAvailability'])->name('availability.save');
    Route::get('/consult-types', [AdminBookingController::class, 'consultTypes'])->name('consultTypes');
    Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
    Route::post('/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('cancel');
});

// ============================================================================
// PREVIEW LAYER - Public-facing location pages (with auth check for drafts)
// ============================================================================

Route::get('/seo/dashboard', [SeoController::class, 'dashboard'])
    ->middleware('auth')
    ->name('seo.dashboard');

Route::get('/preview/{slug}', [LocationPagePreviewController::class, 'show'])
    ->name('location-pages.preview')
    ->where('slug', '[a-z0-9\-]+');

// ============================================================================
// INTERNAL ADMIN LAYER
// ============================================================================
// Handled by Filament at /admin
// See App\Providers\Filament\AdminPanelProvider
// Protected by Filament's built-in authentication
