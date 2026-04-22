<?php

namespace App\Http\Controllers;

use App\Actions\NotifyOwnerOfPurchase;
use App\Jobs\RunQuickScanJob;
use App\Models\FunnelEvent;
use App\Models\QuickScan;
use App\Support\QuickScanReportToken;
use App\Services\Entitlements\EntitlementService;
use App\Services\QuickScanService;
use App\Services\UrlValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Cashier\Cashier;

class QuickScanController extends Controller
{
    public function __construct(private readonly EntitlementService $entitlements)
    {
    }

    /**
     * GET /scan/public/{shareKey}
     * Public, simplified share destination for social links.
     */
    public function publicShare(string $shareKey)
    {
        $scanId = QuickScan::idFromPublicShareKey($shareKey);

        if (!$scanId) {
            abort(404);
        }

        $scan = QuickScan::findOrFail($scanId);

        if (!$scan->matchesPublicShareKey($shareKey)) {
            abort(404);
        }

        if (!$scan->paid || $scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null) {
            abort(404);
        }

        $score = (int) $scan->score;
        $statusStatement = match (true) {
            $score < 40 => 'Critical signal gaps detected',
            $score < 70 => 'AI citation visibility is limited',
            default => 'Your site is not consistently surfaced in AI-driven results',
        };

        $findingCandidates = collect($scan->issues ?? [])->filter(function ($item) {
            return is_string($item) && trim($item) !== '';
        })->map(function ($item) {
            return trim(strip_tags($item));
        })->take(2)->values();

        if ($findingCandidates->isEmpty()) {
            $findingCandidates = collect([
                'Entity and service signals are not clear enough for AI extraction.',
                'Location and coverage structure needs stronger citation-ready formatting.',
            ]);
        }

        $publicUrl = route('scan.public.share', ['shareKey' => $scan->publicShareKey()]);
        $host = parse_url((string) $scan->url, PHP_URL_HOST) ?: 'this site';

        $ogTitle = "AI Visibility Score: {$score}/100";
        $ogDescription = "{$host}: {$statusStatement} Run your AI Visibility Scan for $2.";
        $ogImage = url('/apple-touch-icon.png');

        return view('public.quick-scan-share', [
            'scan' => $scan,
            'score' => $score,
            'statusStatement' => $statusStatement,
            'findings' => $findingCandidates,
            'publicUrl' => $publicUrl,
            'ogTitle' => $ogTitle,
            'ogDescription' => $ogDescription,
            'ogImage' => $ogImage,
        ]);
    }

    /**
     * GET /dashboard/scan/{scan}
     * Authenticated detail route for a user's purchased scan report.
     */
    public function dashboardReport(Request $request, string $scan)
    {
        $scan = $this->resolveDashboardScan($scan);
        $user = Auth::user();
        $sessionId = (string) $request->query('session_id', '');

        if (!$scan || !$user) {
            return $this->renderReadoutUnavailable();
        }

        if ($scan->user_id !== $user->id && $scan->email !== $user->email) {
            return $this->renderReadoutUnavailable();
        }

        // Checkout success can return before webhook completion.
        // Reconcile directly when session context is available.
        if ($sessionId !== '' && (!$scan->paid || $scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null)) {
            $this->reconcileCheckoutAndKickoffScan($scan, $sessionId);
            $scan->refresh();
        }

        if (!$scan->paid) {
            $hasFreshSessionContext = $sessionId !== ''
                && $scan->stripe_session_id === $sessionId
                && $this->isSessionLinkFresh($scan);

            if (!$hasFreshSessionContext) {
                return $this->renderReadoutUnavailable($scan);
            }

            return view('public.quick-scan-processing', [
                'scan' => $scan,
                'sessionId' => $sessionId,
                'resultUrl' => route('dashboard.scans.show', ['scan' => $scan->publicScanId()]),
            ]);
        }

        if ($scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null) {
            return view('public.quick-scan-processing', [
                'scan' => $scan,
                'sessionId' => $sessionId,
                'resultUrl' => route('dashboard.scans.show', ['scan' => $scan->publicScanId()]),
            ]);
        }

        $this->trackResultViews($scan, 'dashboard_scan_report');

        return view('public.quick-scan-result', compact('scan'));
    }

    /**
     * GET /quick-scan
     * Show the scan input form.
     */
    public function show()
    {
        return view('public.quick-scan');
    }

    /**
     * POST /quick-scan/checkout
     * Validate input, gate all checks, then redirect to Stripe.
     */
    public function checkout(Request $request, UrlValidator $urlValidator)
    {
        $ip = $request->ip();

        // ── Honeypot: hidden field must be empty ──────────────────────────
        if ($request->filled('company_website')) {
            Log::warning('QuickScan: honeypot triggered', ['ip' => $ip]);
            return redirect()->route('quick-scan.show');
        }

        // ── Timing check: reject sub-2-second submissions ────────────────
        $formLoadedAt = (float) $request->input('_loaded_at', 0);
        if ($formLoadedAt > 0 && (microtime(true) - $formLoadedAt) < 2.0) {
            Log::warning('QuickScan: timing check failed (too fast)', ['ip' => $ip, 'elapsed' => microtime(true) - $formLoadedAt]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Please take a moment to fill out the form.'])
                ->withInput();
        }

        // ── Rate limit: 5 checkout attempts per IP per 10 minutes ────────
        $rateLimitKey = 'qs-checkout:' . $ip;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            Log::warning('QuickScan: rate limit exceeded', ['ip' => $ip]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Too many attempts. Please wait a few minutes and try again.'])
                ->withInput();
        }
        RateLimiter::hit($rateLimitKey, 600);

        // ── Normalize URL: prepend https:// if bare domain ───────────────
        $rawUrl = trim($request->input('url', ''));
        if ($rawUrl !== '' && !preg_match('#^https?://#i', $rawUrl)) {
            $request->merge(['url' => 'https://' . $rawUrl]);
        }

        // ── Basic validation ─────────────────────────────────────────────
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:500'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
        ], [
            'url.required' => 'Enter a valid website address, such as yoursite.com',
            'url.url' => 'Enter a valid website address, such as yoursite.com',
            'email.email' => 'Enter a valid email address.',
        ]);

        $url = rtrim($validated['url'], '/');
        $email = strtolower(trim($validated['email']));

        // ── URL safety validation (TLD, SSRF, private IPs) ───────────────
        $urlCheck = $urlValidator->validate($url);
        if (!$urlCheck['valid']) {
            Log::info('QuickScan: URL validation failed', ['ip' => $ip, 'url' => $url, 'reason' => $urlCheck['error']]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['url' => $urlCheck['error']])
                ->withInput();
        }

        // ── Pre-payment reachability check ────────────────────────────────
        $reachability = $urlValidator->checkReachability($url);
        if (!$reachability['reachable']) {
            Log::info('QuickScan: reachability check failed', ['ip' => $ip, 'url' => $url, 'reason' => $reachability['error']]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['url' => $reachability['error']])
                ->withInput();
        }

        // ── Rate limit: 3 scans per email per day ────────────────────────
        $emailKey = 'qs-email:' . sha1($email);
        if (RateLimiter::tooManyAttempts($emailKey, 3)) {
            Log::warning('QuickScan: email rate limit exceeded', ['ip' => $ip, 'email' => $email]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'You have reached the scan limit for this email address today. Please try again tomorrow.'])
                ->withInput();
        }
        RateLimiter::hit($emailKey, 86400);

        // ── All gates passed — create pending scan ────────────────────────
        $domain = parse_url($url, PHP_URL_HOST) ?? $url;
        $priorDomainCount = QuickScan::where('domain', $domain)
            ->where('status', QuickScan::STATUS_SCANNED)
            ->count();
        $priorEmailCount = QuickScan::where('email', $email)
            ->where('status', QuickScan::STATUS_SCANNED)
            ->count();
        $isRepeat = $priorDomainCount > 0 || $priorEmailCount > 0;

        $scan = QuickScan::create([
            'email' => $email,
            'url' => $url,
            'domain' => $domain,
            'url_input' => $rawUrl,
            'ip_address' => $ip,
            'user_id' => Auth::id(),
            'status' => QuickScan::STATUS_PENDING,
            'is_repeat_scan' => $isRepeat,
            'domain_scan_count' => $priorDomainCount + 1,
        ]);

        Log::info('QuickScan: checkout initiated', [
            'scan_id' => $scan->id,
            'ip' => $ip,
            'url' => $url,
            'email' => $email,
        ]);

        try {
            $dashboardResultRelative = route('dashboard.scans.show', ['scan' => $scan->publicScanId()], false);
            $dashboardResultWithSession = $dashboardResultRelative
                . (str_contains($dashboardResultRelative, '?') ? '&' : '?')
                . 'session_id={CHECKOUT_SESSION_ID}';

            $successUrl = Auth::check()
                ? url($dashboardResultWithSession)
                : route('login', [
                    'redirect' => $dashboardResultWithSession,
                    'notice' => 'scan-results',
                ]);
            $cancelUrl = url('/quick-scan/cancelled') . '?scan_id=' . $scan->id;

            $session = Cashier::stripe()->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $email,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => 200, // $2.00
                            'product_data' => [
                                'name' => 'AI Citation Quick Scan',
                                'description' => 'Instant AI citation readiness score for ' . parse_url($url, PHP_URL_HOST),
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'scan_id' => $scan->id,
                    'url' => $url,
                    'email' => $email,
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            $scan->update(['stripe_session_id' => $session->id]);

            FunnelEvent::fire(FunnelEvent::SCAN_STARTED, scanId: $scan->id, metadata: [
                'url' => $url,
                'email' => $email,
                'is_repeat' => $isRepeat,
            ]);

            return redirect($session->url);
        } catch (\Throwable $e) {
            Log::error('QuickScan Stripe session failed', [
                'scan_id' => $scan->id,
                'error' => $e->getMessage(),
            ]);
            $scan->update(['status' => QuickScan::STATUS_ERROR]);

            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Payment could not be initiated. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Legacy compatibility endpoint.
     * Redirects into the dedicated authenticated/guest report flows.
     */
    public function result(Request $request)
    {
        $scanId = (int) $request->query('scan_id', 0);
        $sessionId = (string) $request->query('session_id', '');
        $token = (string) $request->query('token', '');

        if (!$scanId) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Invalid result link.']);
        }

        $scan = QuickScan::find($scanId);

        if (!$scan) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Scan record not found.']);
        }

        if (Auth::check()) {
            if (Auth::user()?->isPrivilegedStaff() || Auth::user()?->isFrontendDev()) {
                if ($scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null) {
                    return view('public.quick-scan-processing', [
                        'scan' => $scan,
                        'sessionId' => $scan->stripe_session_id,
                        'resultUrl' => route('report.show', ['scan' => $scan->id]),
                    ]);
                }

                return view('public.quick-scan-result', compact('scan'));
            }

            $isOwner = $scan->user_id === Auth::id() || $scan->email === Auth::user()->email;

            if ($isOwner) {
                return redirect()->route('dashboard.scans.show', ['scan' => $scan->publicScanId()]);
            }
        }

        $query = array_filter([
            'session_id' => $sessionId,
            'token' => $token,
        ]);

        $url = route('report.show', ['scan' => $scan->id]);

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return redirect($url);
    }

    /**
     * GET /report/{scan}?token=...&session_id=...
     * Guest report access with secure token flow.
     */
    public function guestReport(Request $request, QuickScan $scan)
    {
        if (Auth::check()) {
            if (Auth::user()?->isPrivilegedStaff() || Auth::user()?->isFrontendDev()) {
                if ($scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null) {
                    return view('public.quick-scan-processing', [
                        'scan' => $scan,
                        'sessionId' => $scan->stripe_session_id,
                        'resultUrl' => route('report.show', ['scan' => $scan->id]),
                    ]);
                }

                return view('public.quick-scan-result', compact('scan'));
            }

            $isOwner = $scan->user_id === Auth::id() || $scan->email === Auth::user()->email;

            if ($isOwner) {
                return redirect()->route('dashboard.scans.show', ['scan' => $scan->publicScanId()]);
            }
        }

        $sessionId = (string) $request->query('session_id', '');
        $token = (string) $request->query('token', '');
        $hasValidToken = $token !== '' && QuickScanReportToken::isValid($token, $scan);

        if ($sessionId !== '' && $scan->upgrade_status !== 'paid' && $scan->upgrade_stripe_session_id === $sessionId) {
            $this->reconcileCheckoutAndKickoffScan($scan, $sessionId);
            $scan->refresh();
        }

        if (!$scan->paid) {
            $hasValidSession = $sessionId !== ''
                && $scan->stripe_session_id === $sessionId
                && $this->isSessionLinkFresh($scan);

            if (!$hasValidToken && !$hasValidSession) {
                Log::warning('QuickScan: unpaid guest report access denied', [
                    'scan_id' => $scan->id,
                    'provided_session' => $sessionId,
                    'ip' => $request->ip(),
                ]);

                return redirect()->route('quick-scan.show')
                    ->withErrors(['error' => 'Invalid result link.']);
            }

            if ($hasValidSession) {
                $this->reconcileCheckoutAndKickoffScan($scan, $sessionId);
                $scan->refresh();
            }

            if ($scan->paid && $scan->status === QuickScan::STATUS_SCANNED && $scan->score !== null) {
                if ($hasValidToken) {
                    $this->trackResultViews($scan, 'quick_scan_guest_report');
                    session(['last_quick_scan_id' => $scan->id]);
                    return view('public.quick-scan-result', compact('scan'));
                }

                return redirect()->route('report.show', [
                    'scan' => $scan->id,
                    'token' => QuickScanReportToken::generate($scan),
                ]);
            }

            return view('public.quick-scan-processing', [
                'scan' => $scan,
                'sessionId' => $sessionId,
                'token' => $hasValidToken ? $token : '',
                'resultUrl' => route('report.show', ['scan' => $scan->id]),
            ]);
        }

        if ($scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null) {
            $hasValidSession = $sessionId !== ''
                && ($scan->stripe_session_id === $sessionId || $scan->upgrade_stripe_session_id === $sessionId)
                && $this->isSessionLinkFresh($scan);

            if (!$hasValidToken && !$hasValidSession) {
                return redirect()->route('quick-scan.show')
                    ->withErrors(['error' => 'Invalid result link.']);
            }

            return view('public.quick-scan-processing', [
                'scan' => $scan,
                'sessionId' => $sessionId,
                'token' => $hasValidToken ? $token : '',
                'resultUrl' => route('report.show', ['scan' => $scan->id]),
            ]);
        }

        if ($hasValidToken) {
            $this->trackResultViews($scan, 'quick_scan_guest_report');
            session(['last_quick_scan_id' => $scan->id]);
            return view('public.quick-scan-result', compact('scan'));
        }

        $sessionMatches = $sessionId !== ''
            && ($scan->stripe_session_id === $sessionId || $scan->upgrade_stripe_session_id === $sessionId);

        if ($sessionMatches) {
            if (!$this->isSessionLinkFresh($scan)) {
                Log::warning('QuickScan: expired session link denied for guest report', [
                    'scan_id' => $scan->id,
                    'ip' => $request->ip(),
                ]);

                return redirect()->route('quick-scan.show')
                    ->withErrors(['error' => 'Invalid or expired report link.']);
            }

            $secureToken = QuickScanReportToken::generate($scan);

            return redirect()->route('report.show', ['scan' => $scan->id, 'token' => $secureToken]);
        }

        Log::warning('QuickScan: guest token/session verification failed', [
            'scan_id' => $scan->id,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('quick-scan.show')
            ->withErrors(['error' => 'Invalid or expired report link.']);
    }

    /**
     * Ensure a Stripe Checkout session belongs to the exact scan being unlocked.
     */
    private function isCheckoutSessionValidForScan(object $stripeSession, QuickScan $scan, string $sessionId): bool
    {
        $scanEmail = strtolower((string) $scan->email);
        $metadataScanId = (int) ($stripeSession->metadata->scan_id ?? 0);
        $metadataEmail = strtolower((string) ($stripeSession->metadata->email ?? ''));
        $customerEmail = strtolower((string) (($stripeSession->customer_details->email ?? null) ?: ($stripeSession->customer_email ?? '')));

        if ($sessionId === '' || ($stripeSession->id ?? null) !== $sessionId) {
            return false;
        }

        if ($metadataScanId > 0 && $metadataScanId !== (int) $scan->id) {
            return false;
        }

        if ($metadataEmail !== '' && $metadataEmail !== $scanEmail) {
            return false;
        }

        if ($customerEmail !== '' && $customerEmail !== $scanEmail) {
            return false;
        }

        if ($scan->stripe_session_id && $scan->stripe_session_id !== $sessionId) {
            $matchesUpgradeSession = $scan->upgrade_stripe_session_id && $scan->upgrade_stripe_session_id === $sessionId;
            if ($matchesUpgradeSession) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Record result page observability events with score-band context.
     */
    private function trackResultViews(QuickScan $scan, string $sourcePage): void
    {
        $score = (int) ($scan->score ?? 0);
        $scoreBand = $score >= 88 ? 'high' : ($score >= 60 ? 'mid' : 'low');

        $baseMetadata = [
            'score' => (string) $score,
            'score_band' => $scoreBand,
            'source_page' => $sourcePage,
            'user_state' => Auth::check() ? 'logged_in' : 'guest',
            'role' => Auth::check() ? ((Auth::user()?->isPrivilegedStaff() || Auth::user()?->isFrontendDev()) ? 'staff' : 'customer') : 'guest',
        ];

        FunnelEvent::fire(FunnelEvent::RESULT_PAGE_VIEWED, scanId: $scan->id, metadata: $baseMetadata);

        if ($score >= 88) {
            FunnelEvent::fire(FunnelEvent::HIGH_SCORE_RESULT_PAGE_VIEWED, scanId: $scan->id, metadata: $baseMetadata);
        }
    }

    /**
     * GET /quick-scan/status?scan_id=yyy&session_id=cs_xxx
     * JSON polling endpoint for the processing view.
     */
    public function status(Request $request)
    {
        $scanId = $request->query('scan_id');
        $sessionId = (string) $request->query('session_id', '');
        $token = (string) $request->query('token', '');

        if (!$scanId) {
            return response()->json(['ready' => false]);
        }

        $scan = QuickScan::find((int) $scanId);

        if (!$scan) {
            return response()->json(['ready' => false]);
        }

        $isAuthOwner = Auth::check() && ($scan->user_id === Auth::id() || $scan->email === Auth::user()?->email);
        $isSessionOwner = $sessionId !== ''
            && ($scan->stripe_session_id === $sessionId || $scan->upgrade_stripe_session_id === $sessionId);
        if ($isSessionOwner && !$this->isSessionLinkFresh($scan)) {
            $isSessionOwner = false;
        }
        $isTokenOwner = $token !== '' && QuickScanReportToken::isValid($token, $scan);

        if (!$isAuthOwner && !$isSessionOwner && !$isTokenOwner) {
            return response()->json(['ready' => false]);
        }

        if ($isSessionOwner && !$scan->paid && $sessionId !== '') {
            $this->reconcileCheckoutAndKickoffScan($scan, $sessionId);
            $scan->refresh();
        }

        if ($scan->status === QuickScan::STATUS_SCANNED && $scan->score !== null) {
            $reportUrl = $isAuthOwner
                ? route('dashboard.scans.show', ['scan' => $scan->publicScanId()])
                : route('report.show', [
                    'scan' => $scan->id,
                    'token' => $isTokenOwner ? $token : QuickScanReportToken::generate($scan),
                ]);

            return response()->json([
                'ready' => true,
                'score' => $scan->score,
                'report_url' => $reportUrl,
            ]);
        }

        return response()->json(['ready' => false, 'status' => $scan->status]);
    }

    private function isSessionLinkFresh(QuickScan $scan): bool
    {
        return $scan->created_at !== null && $scan->created_at->gte(now()->subHours(24));
    }

    /**
     * Local/dev and webhook-fallback completion path:
     * verifies payment directly with Stripe, marks scan paid, and triggers scan job.
     */
    private function reconcileCheckoutAndKickoffScan(QuickScan $scan, string $sessionId): void
    {
        if ($sessionId === '') {
            return;
        }

        try {
            $stripeSession = Cashier::stripe()->checkout->sessions->retrieve($sessionId, []);

            if (!$this->isCheckoutSessionValidForScan($stripeSession, $scan, $sessionId)) {
                Log::warning('QuickScan: session verification failed during reconcile', [
                    'scan_id' => $scan->id,
                    'session_id' => $sessionId,
                ]);
                return;
            }

            $paymentStatus = (string) ($stripeSession->payment_status ?? '');
            $checkoutStatus = (string) ($stripeSession->status ?? '');
            $isPaid = in_array($paymentStatus, ['paid', 'no_payment_required'], true)
                || $checkoutStatus === 'complete';

            if (!$isPaid) {
                Log::info('QuickScan: reconcile found unpaid checkout session', [
                    'scan_id' => $scan->id,
                    'session_id' => $sessionId,
                    'payment_status' => $paymentStatus,
                    'checkout_status' => $checkoutStatus,
                ]);
                return;
            }

            $upgradePlan = QuickScan::normalizeUpgradePlan((string) ($stripeSession->metadata->upgrade_plan ?? ''));
            if ($upgradePlan !== null) {
                $scan->update([
                    'upgrade_plan' => $upgradePlan,
                    'upgrade_status' => 'paid',
                    'upgrade_stripe_session_id' => $sessionId,
                    'upgraded_at' => now(),
                ]);

                $this->entitlements->issueForScan($scan->fresh());

                return;
            }

            if (!$scan->paid) {
                $scan->update([
                    'paid' => true,
                    'stripe_session_id' => $scan->stripe_session_id ?: $sessionId,
                    'status' => $scan->status === QuickScan::STATUS_PENDING ? QuickScan::STATUS_PAID : $scan->status,
                ]);
                $scan->refresh();

                $this->entitlements->issueForScan($scan);
            }

            if ($scan->status !== QuickScan::STATUS_SCANNED || $scan->score === null) {
                if (app()->environment('local')) {
                    RunQuickScanJob::dispatchSync($scan->id);
                } else {
                    RunQuickScanJob::dispatch($scan->id);
                }
            }
        } catch (\Throwable $e) {
            Log::error('QuickScan: reconcile failed', [
                'scan_id' => $scan->id,
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /quick-scan/cancelled
     * Show a cancelled payment page.
     */
    public function cancelled(Request $request)
    {
        $scanId = (int) $request->query('scan_id', 0);
        $scan = $scanId ? QuickScan::find($scanId) : null;

        // Only expose scan context if session_id matches (prevent enumeration)
        if ($scan && $scan->stripe_session_id !== $request->query('session_id')) {
            $scan = null;
        }

        return view('public.quick-scan-cancelled', ['scan' => $scan]);
    }

    /**
     * Upgrade plan definitions: slug → [label, price in cents].
     */
    private const UPGRADE_PLANS = [
        'diagnostic' => ['Signal Analysis', 9900],
        'fix-strategy' => ['Action Plan', 24900],
        'optimization' => ['Guided Execution', 48900],
    ];

    /**
     * GET /quick-scan/upgrade?plan=diagnostic&scan_id=4&sid=cs_xxx
     * Create Stripe checkout for a scan upgrade and redirect.
     */
    public function upgradeCheckout(Request $request)
    {
        $plan = $request->query('plan');
        $scanId = (int) $request->query('scan_id', 0);
        $sid = (string) $request->query('sid', '');

        if (!$plan || !isset(self::UPGRADE_PLANS[$plan]) || !$scanId) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Invalid upgrade link.']);
        }

        $scan = QuickScan::find($scanId);

        if (!$scan || $scan->status !== QuickScan::STATUS_SCANNED) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Scan not found or not yet complete.']);
        }

        $isStaff = Auth::check() && (Auth::user()?->isPrivilegedStaff() || Auth::user()?->isFrontendDev());
        $isOwner = Auth::check() && ($scan->user_id === Auth::id() || $scan->email === Auth::user()?->email);
        $hasSessionOwnership = $sid !== ''
            && ($scan->stripe_session_id === $sid || $scan->upgrade_stripe_session_id === $sid)
            && $this->isSessionLinkFresh($scan);

        // Verify ownership before any tokenized report redirects to prevent scan_id guessing bypasses.
        if (!$isStaff && !$isOwner && !$hasSessionOwnership) {
            Log::warning('QuickScan: upgrade access denied', [
                'scan_id' => $scan->id,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Invalid upgrade link.']);
        }

        $requestedRank = QuickScan::rankForUpgradePlan($plan);
        $currentRank = $scan->upgradeTierRank();
        if ($scan->upgrade_status === 'paid' && $requestedRank > 0 && $requestedRank <= $currentRank) {
            if ($isOwner || $isStaff) {
                return redirect()->route('dashboard.scans.show', ['scan' => $scan->publicScanId()])
                    ->with('status', 'This layer is already unlocked for this scan.');
            }

            return redirect()->route('report.show', [
                'scan' => $scan->id,
                'token' => QuickScanReportToken::generate($scan),
            ])->with('status', 'This layer is already unlocked for this scan.');
        }

        // Verify baseline session ownership for new checkout creation.
        if ($sid === '' || $scan->stripe_session_id !== $sid || !$this->isSessionLinkFresh($scan)) {
            Log::warning('QuickScan: upgrade sid mismatch', [
                'scan_id' => $scan->id,
                'ip' => $request->ip(),
            ]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Invalid upgrade link.']);
        }

        [$productName, $unitAmount] = self::UPGRADE_PLANS[$plan];

        try {
            if (Auth::check()) {
                $successBaseUrl = route('dashboard.scans.show', ['scan' => $scan->publicScanId()]);
                $successUrl = $successBaseUrl . (str_contains($successBaseUrl, '?') ? '&' : '?') . 'session_id={CHECKOUT_SESSION_ID}';
            } else {
                $successUrl = route('login', [
                    'redirect' => route('dashboard.scans.show', ['scan' => $scan->publicScanId()], false),
                    'notice' => 'scan-results',
                ]);
            }
            $cancelUrl = route('report.show', ['scan' => $scan->id]) . '?session_id=' . urlencode((string) ($scan->stripe_session_id ?? 'none'));

            $session = Cashier::stripe()->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $scan->email,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => $unitAmount,
                            'product_data' => [
                                'name' => $productName . ' — ' . parse_url($scan->url, PHP_URL_HOST),
                                'description' => $productName . ' upgrade for scan #' . $scan->id,
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'scan_id' => $scan->id,
                    'upgrade_plan' => $plan,
                    'url' => $scan->url,
                    'email' => $scan->email,
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            $scan->update([
                'upgrade_plan' => QuickScan::normalizeUpgradePlan($plan),
                'upgrade_status' => 'pending',
                'upgrade_stripe_session_id' => $session->id,
            ]);

            FunnelEvent::fire(FunnelEvent::UPGRADE_CLICK, scanId: $scan->id, metadata: [
                'plan' => $plan,
                'amount' => (string) $unitAmount,
                'score_band' => ((int) ($scan->score ?? 0)) >= 88 ? 'high' : (((int) ($scan->score ?? 0)) >= 60 ? 'mid' : 'low'),
                'source_page' => 'quick_scan_result',
                'user_state' => Auth::check() ? 'logged_in' : 'guest',
                'role' => Auth::check() ? ((Auth::user()?->isPrivilegedStaff() || Auth::user()?->isFrontendDev()) ? 'staff' : 'customer') : 'guest',
            ]);

            FunnelEvent::fire(FunnelEvent::CHECKOUT_ENTRY, scanId: $scan->id, metadata: [
                'flow' => 'quick_scan_upgrade',
                'tier' => $plan,
                'amount_cents' => (string) $unitAmount,
                'source_page' => 'quick_scan_result',
                'user_state' => Auth::check() ? 'logged_in' : 'guest',
                'role' => Auth::check() ? ((Auth::user()?->isPrivilegedStaff() || Auth::user()?->isFrontendDev()) ? 'staff' : 'customer') : 'guest',
            ]);

            Log::info('QuickScan: upgrade checkout initiated', [
                'scan_id' => $scan->id,
                'plan' => $plan,
                'amount' => $unitAmount,
                'session' => $session->id,
            ]);

            return redirect($session->url);
        } catch (\Throwable $e) {
            Log::error('QuickScan: upgrade checkout failed', [
                'scan_id' => $scan->id,
                'plan' => $plan,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['error' => 'Upgrade payment could not be initiated. Please try again.']);
        }
    }

    private function resolveDashboardScan(string $scanRef): ?QuickScan
    {
        $scanRef = trim($scanRef);

        if ($scanRef === '') {
            return null;
        }

        $scanId = QuickScan::idFromPublicReference($scanRef);
        if (!$scanId) {
            return null;
        }

        return QuickScan::find($scanId);
    }

    private function renderReadoutUnavailable(?QuickScan $scan = null)
    {
        $latestScan = Auth::user()
            ? QuickScan::query()
                ->where(function ($query) {
                    $query->where('user_id', Auth::id())
                        ->orWhere('email', Auth::user()?->email);
                })
                ->where('status', QuickScan::STATUS_SCANNED)
                ->whereNotNull('score')
                ->latest('scanned_at')
                ->latest('id')
                ->first()
            : null;

        return response()->view('public.system-readout-unavailable', [
            'scan' => $scan,
            'latestScanUrl' => $latestScan
                ? route('dashboard.scans.show', ['scan' => $latestScan->publicScanId()])
                : route('quick-scan.show'),
            'refreshUrl' => request()->fullUrl(),
        ], 200);
    }
}
