<?php

namespace App\Http\Controllers;

use App\Jobs\RunQuickScanJob;
use App\Models\FunnelEvent;
use App\Models\QuickScan;
use App\Services\QuickScanService;
use App\Services\UrlValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Cashier\Cashier;

class QuickScanController extends Controller
{
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
            $successUrl = url('/quick-scan/result') . '?session_id={CHECKOUT_SESSION_ID}&scan_id=' . $scan->id;
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
     * GET /quick-scan/result?session_id=cs_xxx&scan_id=yyy
     * Verify payment, run scan, show results.
     * Never throws 500 — falls back to processing view on any failure.
     */
    public function result(Request $request, QuickScanService $scanner)
    {
        $sessionId = $request->query('session_id');
        $scanId = $request->query('scan_id');

        // ── Phase 7: validate query params ───────────────────────────────
        if (!$sessionId || !$scanId) {
            Log::warning('QuickScan: result page missing params', [
                'session_id' => $sessionId,
                'scan_id' => $scanId,
            ]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Invalid result link.']);
        }

        // ── Phase 1: safe retrieval ──────────────────────────────────────
        $scan = QuickScan::find((int) $scanId);

        if (!$scan) {
            Log::warning('QuickScan: scan not found on result page', ['scan_id' => $scanId]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Scan record not found.']);
        }

        // ── Phase 2: already complete — show results ─────────────────────
        if ($scan->status === QuickScan::STATUS_SCANNED && $scan->score !== null) {
            // Check if returning from upgrade checkout
            if ($sessionId && $scan->upgrade_stripe_session_id === $sessionId && $scan->upgrade_status !== 'paid') {
                try {
                    $upgradeSession = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
                    if (($upgradeSession->payment_status ?? null) === 'paid') {
                        $scan->update([
                            'upgrade_status' => 'paid',
                            'upgraded_at' => now(),
                        ]);
                        $scan->refresh();
                        Log::info('QuickScan: upgrade payment confirmed', [
                            'scan_id' => $scan->id,
                            'plan' => $scan->upgrade_plan,
                            'session' => $sessionId,
                        ]);

                        FunnelEvent::fire(FunnelEvent::UPGRADE_PURCHASED, scanId: $scan->id, metadata: [
                            'plan' => $scan->upgrade_plan,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::error('QuickScan: upgrade session retrieval failed', [
                        'session_id' => $sessionId,
                        'scan_id' => $scan->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            Log::info('QuickScan: result page revisited', ['scan_id' => $scan->id]);
            return view('public.quick-scan-result', compact('scan'));
        }

        // ── Phase 8: verify payment with Stripe ─────────────────────────
        if (!$scan->paid) {
            try {
                $stripeSession = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

                if (($stripeSession->payment_status ?? null) !== 'paid') {
                    Log::info('QuickScan: payment not yet confirmed', [
                        'scan_id' => $scan->id,
                        'session_id' => $sessionId,
                        'payment_status' => $stripeSession->payment_status ?? 'unknown',
                    ]);
                    // Show processing view — webhook may complete payment later
                    return view('public.quick-scan-processing', [
                        'scan' => $scan,
                        'sessionId' => $sessionId,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('QuickScan: Stripe session retrieval failed', [
                    'session_id' => $sessionId,
                    'scan_id' => $scan->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't crash — show processing view, webhook will handle it
                return view('public.quick-scan-processing', [
                    'scan' => $scan,
                    'sessionId' => $sessionId,
                ]);
            }

            // Mark paid
            $scan->update([
                'paid' => true,
                'stripe_session_id' => $sessionId,
                'status' => QuickScan::STATUS_PAID,
            ]);
        }

        Log::info('QuickScan: payment verified, starting scan', [
            'scan_id' => $scan->id,
            'url' => $scan->url,
            'email' => $scan->email,
            'ip' => $scan->ip_address,
            'stripe_session' => $sessionId,
        ]);

        // ── Run scan synchronously — wrapped in try/catch ────────────────
        try {
            $result = $scanner->scan($scan->url);

            // Scan memory: capture previous score for same domain
            $previousScan = QuickScan::where('domain', $scan->domain)
                ->where('status', QuickScan::STATUS_SCANNED)
                ->where('id', '!=', $scan->id)
                ->latest('scanned_at')
                ->first();
            $lastScore = $previousScan?->score;
            $scoreChange = $lastScore !== null ? ($result['score'] - $lastScore) : null;

            $scan->update([
                'score' => $result['score'],
                'last_score' => $lastScore,
                'score_change' => $scoreChange,
                'categories' => $result['categories'],
                'issues' => $result['issues'],
                'strengths' => $result['strengths'],
                'fastest_fix' => $result['fastest_fix'],
                'raw_checks' => $result['raw_checks'],
                'broken_links' => $result['broken_links'],
                'page_count' => $result['page_count'],
                'status' => QuickScan::STATUS_SCANNED,
                'scanned_at' => now(),
            ]);
            $scan->refresh();

            Log::info('QuickScan: scan completed, result delivered', [
                'scan_id' => $scan->id,
                'score' => $scan->score,
                'url' => $scan->url,
                'email' => $scan->email,
                'ip' => $scan->ip_address,
            ]);

            FunnelEvent::fire(FunnelEvent::SCAN_COMPLETED, scanId: $scan->id, metadata: [
                'score' => $scan->score,
                'url' => $scan->url,
            ]);
        } catch (\Throwable $e) {
            Log::error('QuickScan: synchronous scan failed', [
                'scan_id' => $scan->id,
                'url' => $scan->url,
                'error' => $e->getMessage(),
            ]);
            // Dispatch job as fallback — show processing view
            RunQuickScanJob::dispatch($scan->id);
            return view('public.quick-scan-processing', [
                'scan' => $scan,
                'sessionId' => $sessionId,
            ]);
        }

        // Dispatch job for CRM upsert + email sequence (idempotent)
        RunQuickScanJob::dispatch($scan->id);

        return view('public.quick-scan-result', compact('scan'));
    }

    /**
     * GET /quick-scan/status?scan_id=yyy
     * JSON polling endpoint for the processing view.
     */
    public function status(Request $request)
    {
        $scanId = $request->query('scan_id');

        if (!$scanId) {
            return response()->json(['ready' => false]);
        }

        $scan = QuickScan::find((int) $scanId);

        if (!$scan) {
            return response()->json(['ready' => false]);
        }

        if ($scan->status === QuickScan::STATUS_SCANNED && $scan->score !== null) {
            return response()->json(['ready' => true, 'score' => $scan->score]);
        }

        return response()->json(['ready' => false, 'status' => $scan->status]);
    }

    /**
     * GET /quick-scan/cancelled
     * Show a cancelled payment page.
     */
    public function cancelled(Request $request)
    {
        $scanId = (int) $request->query('scan_id', 0);
        $scan = $scanId ? QuickScan::find($scanId) : null;

        return view('public.quick-scan-cancelled', ['scan' => $scan]);
    }

    /**
     * Upgrade plan definitions: slug → [label, price in cents].
     */
    private const UPGRADE_PLANS = [
        'diagnostic' => ['Signal Expansion', 9900],
        'fix-strategy' => ['Structural Leverage', 24900],
        'optimization' => ['System Activation', 48900],
    ];

    /**
     * GET /quick-scan/upgrade?plan=diagnostic&scan_id=4
     * Create Stripe checkout for a scan upgrade and redirect.
     */
    public function upgradeCheckout(Request $request)
    {
        $plan = $request->query('plan');
        $scanId = (int) $request->query('scan_id', 0);

        if (!$plan || !isset(self::UPGRADE_PLANS[$plan]) || !$scanId) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Invalid upgrade link.']);
        }

        $scan = QuickScan::find($scanId);

        if (!$scan || $scan->status !== QuickScan::STATUS_SCANNED) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Scan not found or not yet complete.']);
        }

        [$productName, $unitAmount] = self::UPGRADE_PLANS[$plan];

        try {
            $successUrl = url('/quick-scan/result') . '?session_id={CHECKOUT_SESSION_ID}&scan_id=' . $scan->id;
            $cancelUrl = url('/quick-scan/result') . '?session_id=' . ($scan->stripe_session_id ?? 'none') . '&scan_id=' . $scan->id;

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
                'upgrade_plan' => $plan,
                'upgrade_status' => 'pending',
                'upgrade_stripe_session_id' => $session->id,
            ]);

            FunnelEvent::fire(FunnelEvent::UPGRADE_CLICK, scanId: $scan->id, metadata: [
                'plan' => $plan,
                'amount' => $unitAmount,
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
}
