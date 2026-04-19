<?php

namespace App\Http\Controllers;

use App\Actions\NotifyOwnerOfPurchase;
use App\Enums\SystemTier;
use App\Jobs\SendUpgradeFunnelEmailsJob;
use App\Jobs\RunQuickScanJob;
use App\Models\FunnelEvent;
use App\Models\QuickScan;
use App\Models\User;
use App\Support\QuickScanReportToken;
use App\Services\Entitlements\EntitlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    public function __construct(private readonly EntitlementService $entitlements)
    {
    }

    /**
     * Legacy checkout route definitions.
     *
     * Model decision (A): keep historical route slugs and pricing internally,
     * while mapping everything above scan-basic into the single Activation path.
     */
    private const TIERS = [
        'scan-basic' => [
            'name' => 'AI Citation Quick Scan',
            'amount' => 200,
            'system_tier' => 'scan-basic',
            'product_path' => 'consultation',
            'upgrade_plan' => null,
        ],
        'signal-expansion' => [
            'name' => 'Full System Activation',
            'amount' => 9900,
            'system_tier' => 'signal-expansion',
            'product_path' => 'activation',
            'upgrade_plan' => 'diagnostic',
        ],
        'structural-leverage' => [
            'name' => 'Full System Activation',
            'amount' => 24900,
            'system_tier' => 'structural-leverage',
            'product_path' => 'activation',
            'upgrade_plan' => 'fix-strategy',
        ],
        'system-activation' => [
            'name' => 'Full System Activation',
            'amount' => 48900,
            'system_tier' => 'system-activation',
            'product_path' => 'activation',
            'upgrade_plan' => 'optimization',
        ],
    ];

    private function mapCheckoutTierToScanUpgradePlan(string $tierSlug): ?string
    {
        return self::TIERS[$tierSlug]['upgrade_plan'] ?? null;
    }

    private function productPathForTier(string $tierSlug): string
    {
        return self::TIERS[$tierSlug]['product_path'] ?? 'activation';
    }

    public function scanBasic(Request $request)
    {
        // Pull pre-collected URL + email from scan entry flow (if present)
        $scanEntry = Session::get('scan_entry');
        $extra = [];
        if ($scanEntry) {
            $extra['customer_email'] = $scanEntry['email'];
            $extra['metadata_extra'] = [
                'scan_url' => $scanEntry['url'],
                'scan_email' => $scanEntry['email'],
            ];
        }

        return $this->redirectToStripe('scan-basic', $request, $extra);
    }

    public function signalExpansion(Request $request)
    {
        return $this->redirectToStripe('signal-expansion', $request);
    }

    public function structuralLeverage(Request $request)
    {
        return $this->redirectToStripe('structural-leverage', $request);
    }

    public function systemActivation(Request $request)
    {
        return $this->redirectToStripe('system-activation', $request);
    }

    /**
     * GET /checkout/complete?session_id=cs_xxx
     *
     * Unified post-payment handler for all tiers.
     * Verifies payment, creates or authenticates user, sets system tier,
     * stores scan session, and redirects to dashboard.
     */
    public function complete(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect('/pricing')->with('error', 'Invalid checkout link.');
        }

        try {
            $stripeSession = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
        } catch (\Throwable $e) {
            Log::error('CheckoutController: Stripe session retrieval failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            return redirect('/pricing')->with('error', 'Could not verify payment. Please contact support.');
        }

        if (($stripeSession->payment_status ?? null) !== 'paid') {
            Log::info('CheckoutController: payment not confirmed', [
                'session_id' => $sessionId,
                'status' => $stripeSession->payment_status ?? 'unknown',
            ]);
            return redirect('/pricing')->with('error', 'Payment not yet confirmed. Please try again or contact support.');
        }

        $tierSlug = $stripeSession->metadata->tier ?? null;
        $email = $stripeSession->customer_details->email ?? null;
        $scanUrl = $stripeSession->metadata->scan_url ?? null;

        if (!$tierSlug || !$email || !isset(self::TIERS[$tierSlug])) {
            Log::error('CheckoutController: missing tier or email from Stripe session', [
                'session_id' => $sessionId,
                'tier' => $tierSlug,
                'email' => $email,
            ]);
            return redirect('/pricing')->with('error', 'Invalid checkout session.');
        }

        $systemTier = SystemTier::tryFrom($tierSlug);
        if (!$systemTier) {
            return redirect('/pricing')->with('error', 'Invalid tier.');
        }

        // Scan-basic guest flow: keep user as guest and route to secure report URL.
        if ($tierSlug === 'scan-basic' && !Auth::check()) {
            $existingScan = QuickScan::where('stripe_session_id', $sessionId)->first();

            if ($existingScan) {
                $scan = $existingScan;
            } else {
                $scanUrl = $scanUrl ?: ('https://' . Str::after($email, '@'));
                $domain = parse_url($scanUrl, PHP_URL_HOST);

                $scan = QuickScan::create([
                    'email' => $email,
                    'url' => $scanUrl,
                    'domain' => $domain,
                    'stripe_session_id' => $sessionId,
                    'paid' => true,
                    'status' => QuickScan::STATUS_PAID,
                    'source' => 'checkout_' . $tierSlug,
                ]);
            }

            if (!$scan->paid) {
                $scan->update([
                    'paid' => true,
                    'status' => QuickScan::STATUS_PAID,
                    'stripe_session_id' => $sessionId,
                ]);
            }

            $this->entitlements->issueForScan($scan->fresh());

            FunnelEvent::fire(FunnelEvent::PAYMENT_SUCCESS, scanId: $scan->id, metadata: [
                'flow' => 'direct_checkout',
                'tier' => $tierSlug,
                'amount_cents' => (string) self::TIERS[$tierSlug]['amount'],
                'source_page' => 'checkout_complete',
                'user_state' => 'guest',
                'role' => 'guest',
            ]);

            if (app()->environment('local')) {
                RunQuickScanJob::dispatchSync($scan->id);
            } else {
                RunQuickScanJob::dispatch($scan->id);
            }
            (new NotifyOwnerOfPurchase)->execute($scan, self::TIERS[$tierSlug]['name'], self::TIERS[$tierSlug]['amount']);

            return redirect()->route('report.show', [
                'scan' => $scan->id,
                'token' => QuickScanReportToken::generate($scan),
            ])->with('system_entry', $systemTier->value);
        }

        // Prevent replay — check if this session was already processed
        $existingUser = User::where('stripe_checkout_session_id', $sessionId)->first();
        if ($existingUser) {
            Auth::login($existingUser, true);
            return redirect('/dashboard');
        }

        $email = strtolower(trim($email));

        // Find or create user
        $user = User::where('email', $email)->first();
        $isNew = false;

        if (!$user) {
            $user = User::create([
                'name' => Str::before($email, '@'),
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'role' => 'buyer',
                'approved' => true,
                'is_active' => true,
                'onboarding_completed_at' => now(),
                'system_tier' => $systemTier,
                'system_tier_upgraded_at' => now(),
                'stripe_checkout_session_id' => $sessionId,
                'signup_ip' => $request->ip(),
                'signup_user_agent' => $request->userAgent(),
                'signup_source' => 'checkout',
                'signup_referrer' => $request->headers->get('referer'),
            ]);
            $isNew = true;
        } else {
            // Existing user — upgrade tier if higher, approve, mark session
            $user->upgradeSystemTier($systemTier);
            $user->update([
                'approved' => true,
                'is_active' => true,
                'stripe_checkout_session_id' => $sessionId,
                'onboarding_completed_at' => $user->onboarding_completed_at ?? now(),
            ]);
        }

        // For upgrade tiers, try to upgrade the user's existing scan instead of creating a duplicate
        $existingScan = $tierSlug !== 'scan-basic'
            ? QuickScan::where('user_id', $user->id)->latest()->first()
            : null;

        $requestedUpgradePlan = $this->mapCheckoutTierToScanUpgradePlan($tierSlug);
        $requestedUpgradeRank = QuickScan::rankForUpgradePlan($requestedUpgradePlan);

        if ($existingScan && $tierSlug !== 'scan-basic') {
            $currentRank = $existingScan->upgradeTierRank();
            $planToPersist = $requestedUpgradeRank >= $currentRank
                ? $requestedUpgradePlan
                : $existingScan->normalizedUpgradePlan();

            $existingScan->update([
                'stripe_session_id' => $sessionId,
                'paid' => true,
                'source' => 'checkout_' . $tierSlug,
                'upgrade_plan' => $planToPersist,
                'upgrade_status' => 'paid',
                'upgraded_at' => now(),
            ]);
            $scan = $existingScan;
        } else {
            // Fallback: resolve URL from scan entry session or user's latest scan
            if (!$scanUrl) {
                $scanUrl = QuickScan::where('user_id', $user->id)->latest()->value('url');
            }
            $scanUrl = $scanUrl ?: ('https://' . Str::after($email, '@'));

            $domain = parse_url($scanUrl, PHP_URL_HOST);

            $scan = QuickScan::create([
                'email' => $email,
                'url' => $scanUrl,
                'domain' => $domain,
                'user_id' => $user->id,
                'stripe_session_id' => $sessionId,
                'paid' => true,
                'status' => $tierSlug === 'scan-basic' ? QuickScan::STATUS_PAID : QuickScan::STATUS_SCANNED,
                'source' => 'checkout_' . $tierSlug,
                'upgrade_plan' => $requestedUpgradePlan,
                'upgrade_status' => $tierSlug === 'scan-basic' ? null : 'paid',
                'upgraded_at' => $tierSlug === 'scan-basic' ? null : now(),
            ]);
        }

        Auth::login($user, true);

        $this->entitlements->issueForUserTier($user);
        $this->entitlements->issueForScan($scan->fresh());

        // Clear scan entry session data
        Session::forget('scan_entry');

        $user->update(['last_login_at' => now()]);

        FunnelEvent::fire('checkout_completed', userId: $user->id, scanId: $scan->id, metadata: [
            'tier' => $tierSlug,
            'amount_cents' => self::TIERS[$tierSlug]['amount'],
            'is_new_user' => $isNew,
            'stripe_session_id' => $sessionId,
        ]);

        FunnelEvent::fire(FunnelEvent::PAYMENT_SUCCESS, userId: $user->id, scanId: $scan->id, metadata: [
            'flow' => 'direct_checkout',
            'tier' => $tierSlug,
            'amount_cents' => (string) self::TIERS[$tierSlug]['amount'],
            'source_page' => 'checkout_complete',
            'user_state' => 'logged_in',
            'role' => ($user->isPrivilegedStaff() || $user->isFrontendDev()) ? 'staff' : 'customer',
        ]);

        // Dispatch tier-specific funnel email sequence
        SendUpgradeFunnelEmailsJob::dispatch($scan->id, $user->id, $tierSlug);

        // Notify owner of purchase
        (new NotifyOwnerOfPurchase)->execute($scan, self::TIERS[$tierSlug]['name'], self::TIERS[$tierSlug]['amount']);

        Log::info('CheckoutController: unified entry completed', [
            'user_id' => $user->id,
            'tier' => $tierSlug,
            'is_new' => $isNew,
            'scan_id' => $scan->id,
        ]);

        if ($tierSlug === 'scan-basic') {
            if (app()->environment('local')) {
                RunQuickScanJob::dispatchSync($scan->id);
            } else {
                RunQuickScanJob::dispatch($scan->id);
            }

            return redirect()->route('dashboard.scans.show', ['scan' => $scan->publicScanId()])
                ->with('system_entry', $systemTier->value);
        }

        return redirect('/dashboard')->with('system_entry', $systemTier->value);
    }

    /**
     * Build a Stripe Checkout session and redirect.
     * All tiers redirect to /checkout/complete on success.
     */
    private function redirectToStripe(string $tierSlug, Request $request, array $extra = [])
    {
        $tier = self::TIERS[$tierSlug];

        FunnelEvent::fire(FunnelEvent::CHECKOUT_ENTRY, metadata: [
            'flow' => 'direct_checkout',
            'tier' => $tierSlug,
            'product_path' => $this->productPathForTier($tierSlug),
            'amount_cents' => (string) $tier['amount'],
            'source_page' => (string) ($request->headers->get('referer') ?: 'direct'),
            'user_state' => Auth::check() ? 'logged_in' : 'guest',
            'role' => Auth::check() ? ((Auth::user()?->isPrivilegedStaff() || Auth::user()?->isFrontendDev()) ? 'staff' : 'customer') : 'guest',
        ]);

        $metadata = [
            'tier' => $tierSlug,
            'product_path' => $this->productPathForTier($tierSlug),
            'ip' => $request->ip(),
            'ref' => $request->query('ref', 'direct'),
        ];

        // For direct checkout upgrades (signal/structural/system), attach scan + upgrade metadata
        // so the Quick Scan webhook can reconcile payment even when return redirect is missed.
        if ($tierSlug !== 'scan-basic') {
            $upgradePlan = $this->mapCheckoutTierToScanUpgradePlan($tierSlug);
            if ($upgradePlan) {
                $metadata['upgrade_plan'] = $upgradePlan;
            }

            if (Auth::check()) {
                $latestScan = QuickScan::where('user_id', Auth::id())->latest()->first();
                if ($latestScan) {
                    $metadata['scan_id'] = (string) $latestScan->id;
                }
            }
        }

        // Merge scan entry data into metadata if provided
        if (!empty($extra['metadata_extra'])) {
            $metadata = array_merge($metadata, $extra['metadata_extra']);
        }

        $sessionParams = [
            'mode' => 'payment',
            'success_url' => url('/checkout/complete') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('/pricing'),
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $tier['amount'],
                        'product_data' => [
                            'name' => $tier['name'],
                            'description' => 'SEO AI Co™ — ' . $tier['name'],
                        ],
                    ],
                    'quantity' => 1,
                ]
            ],
            'metadata' => $metadata,
            'payment_intent_data' => [
                'metadata' => [
                    'tier' => $tierSlug,
                    'product_path' => $this->productPathForTier($tierSlug),
                    'upgrade_plan' => (string) ($metadata['upgrade_plan'] ?? ''),
                    'scan_id' => (string) ($metadata['scan_id'] ?? ''),
                ],
            ],
        ];

        // Pre-fill customer email if provided via scan entry flow
        if (!empty($extra['customer_email'])) {
            $sessionParams['customer_email'] = $extra['customer_email'];
        }

        try {
            $session = Cashier::stripe()->checkout->sessions->create($sessionParams);

            return redirect($session->url);
        } catch (\Throwable $e) {
            Log::error('CheckoutController: Stripe session failed', [
                'tier' => $tierSlug,
                'error' => $e->getMessage(),
            ]);

            return redirect('/pricing')->with('error', 'Unable to start checkout. Please try again.');
        }
    }
}
