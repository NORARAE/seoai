<?php

use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingManageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationPagePreviewController;
use App\Http\Controllers\MarketingPageController;
use App\Http\Controllers\MarketingSitemapController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserOnboardingController;
use App\Http\Middleware\EnsureOnboardingComplete;
use App\Http\Controllers\PublicSitemapController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\UnsubscribeController;
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
Route::get('/login', fn() => redirect('/admin/login'))->name('login');

// Google OAuth sign-in — routes registered regardless of enabled flag;
// the controller itself returns 404 when GOOGLE_LOGIN_ENABLED=false.
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/privacy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PublicController::class, 'terms'])->name('terms');
Route::post('/licensing-inquiry', [PublicController::class, 'storeLicensingInquiry'])
    ->middleware('throttle:inquiry')
    ->name('licensing-inquiry.store');
Route::get('/licensing-inquiry', fn() => redirect(url('/') . '#contact'))->name('licensing-inquiry.get');

Route::get('/checkout/success', fn() => view('public.checkout-success'))->name('checkout.success');
Route::get('/checkout/cancelled', fn() => view('public.checkout-cancelled'))->name('checkout.cancelled');
Route::get('/rd-tax-credit', fn() => view('public.rd-tax-credit'))->name('rd-tax-credit');

// ── Sitemap: static/scaffold pages (all routes in the locked sitemap) ──
Route::get('/how-it-works', [PublicController::class, 'howItWorks'])->name('how-it-works');
Route::get('/solutions', [PublicController::class, 'solutions'])->name('solutions');
Route::get('/solutions/agencies', [PublicController::class, 'solutionsAgencies'])->name('solutions.agencies');
Route::get('/solutions/business-owners', [PublicController::class, 'solutionsBusinessOwners'])->name('solutions.business-owners');
Route::get('/access', fn() => redirect('/onboarding/start'))->name('access');

// ── Execution service pages ──
Route::get('/growth-services', [PublicController::class, 'growthServices'])->name('growth-services');
Route::get('/web-design-development', [PublicController::class, 'webDesignDevelopment'])->name('web-design-development');
Route::get('/wordpress-support', [PublicController::class, 'wordpressSupport'])->name('wordpress-support');
Route::get('/ads-management', [PublicController::class, 'adsManagement'])->name('ads-management');
Route::get('/branding-print', [PublicController::class, 'brandingPrint'])->name('branding-print');
Route::get('/access-plans', [PublicController::class, 'accessPlans'])->name('access-plans');
Route::get('/ai-seo-for-chatgpt-geo-aeo', [PublicController::class, 'aiSeoGeoAeo'])->name('ai-seo-geo-aeo');

// ── Booking / Consult System ──
Route::post('/track/modal-open', [TrackingController::class, 'modalOpen'])->middleware('throttle:30,1')->name('track.modal-open');
Route::get('/book', [BookingController::class, 'index'])->name('book.index');
Route::get('/book/slots', [BookingController::class, 'getSlots'])->middleware('throttle:booking')->name('book.slots');
Route::post('/book', [BookingController::class, 'store'])->middleware('throttle:booking')->name('book.store');
Route::post('/book/checkout', [BookingController::class, 'initiateCheckout'])->middleware('throttle:booking')->name('book.checkout');
Route::get('/book/confirmed', [BookingController::class, 'confirmed'])->name('book.confirmed');
Route::get('/book/upgrade', [BookingController::class, 'upgrade'])->name('book.upgrade');
Route::get('/book/confirmation', fn() => redirect()->route('book.confirmed', ['booking' => request('booking')]))->name('book.confirmation');
Route::get('/book/payment-return/{booking}', [BookingController::class, 'handlePaymentReturn'])->name('book.payment-return');
Route::get('/book/confirm/{booking}', [BookingController::class, 'confirm'])->name('book.confirm');
Route::get('/book/cancel/{booking}', [BookingController::class, 'cancel'])->name('book.cancel');
Route::post('/book/cancel/{booking}', [BookingController::class, 'processCancel'])->name('book.processCancel');

// Self-service booking management (token-based, no auth required)
Route::get('/booking/manage/{token}', [BookingManageController::class, 'show'])->name('booking.manage');
Route::post('/booking/reschedule/{token}', [BookingManageController::class, 'reschedule'])->middleware('throttle:10,1')->name('booking.reschedule');
Route::delete('/booking/cancel/{token}', [BookingManageController::class, 'cancel'])->middleware('throttle:5,1')->name('booking.cancel');

// ── Onboarding Flow ──
// Throttle submit to 5 requests per minute to prevent abuse.
Route::get('/onboarding/start', [OnboardingController::class, 'start'])->name('onboarding.start');
Route::post('/onboarding/submit', [OnboardingController::class, 'submit'])->middleware('throttle:5,1')->name('onboarding.submit');
Route::get('/onboarding/done', [OnboardingController::class, 'done'])->name('onboarding.done');
// Admin-only secure license file download (auth required — never served publicly)
Route::middleware(['auth'])->get('/onboarding/license/{submission}', [OnboardingController::class, 'downloadLicense'])->name('onboarding.license.download');

// ── Email unsubscribe ──
Route::get('/unsubscribe/{token}', [UnsubscribeController::class, 'unsubscribe'])
    ->middleware('throttle:10,1')
    ->name('unsubscribe');

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
Route::middleware('auth')->get('/pending-approval', fn() => view('pending-approval'))
    ->name('pending-approval');

// Post-approval user onboarding (workspace setup)
Route::middleware(['auth', EnsureUserIsApproved::class])->group(function () {
    Route::get('/setup', [UserOnboardingController::class, 'show'])->name('user.onboarding');
    Route::post('/setup', [UserOnboardingController::class, 'store'])->name('user.onboarding.store');
});

Route::middleware(['auth', EnsureUserIsApproved::class, EnsureOnboardingComplete::class])->prefix('dashboard')->name('app.')->group(function () {

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

// ============================================================================
// SEO MARKETING PAGES — sitemap routes first, wildcard slug LAST
// ============================================================================

Route::get('/sitemap.xml', [MarketingSitemapController::class, 'index'])
    ->name('seo.sitemap.index');

Route::get('/sitemaps/marketing-{cluster}.xml', [MarketingSitemapController::class, 'cluster'])
    ->where('cluster', 'core|agency|local|strategy|industry')
    ->name('seo.sitemap.cluster');

// Wildcard slug MUST be registered last to avoid capturing app routes above
Route::get('/{slug}', [MarketingPageController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('seo.page.show');
