<?php

namespace App\Http\Controllers;

use App\Enums\SystemTier;
use App\Jobs\SendUpgradeFunnelEmailsJob;
use App\Models\FunnelEvent;
use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    /**
     * Tier definitions: slug → [name, amount in cents, system_tier].
     */
    private const TIERS = [
        'scan-basic' => [
            'name' => 'AI Citation Quick Scan',
            'amount' => 200,
            'system_tier' => 'scan-basic',
        ],
        'signal-expansion' => [
            'name' => 'Signal Expansion — Full Analysis',
            'amount' => 9900,
            'system_tier' => 'signal-expansion',
        ],
        'structural-leverage' => [
            'name' => 'Structural Leverage — Fix Strategy',
            'amount' => 24900,
            'system_tier' => 'structural-leverage',
        ],
        'system-activation' => [
            'name' => 'System Activation — Full Deployment',
            'amount' => 48900,
            'system_tier' => 'system-activation',
        ],
    ];

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

        // Determine URL and domain from scan entry flow or metadata
        $domain = $scanUrl ? parse_url($scanUrl, PHP_URL_HOST) : null;

        // Create scan session record
        $scan = QuickScan::create([
            'email' => $email,
            'url' => $scanUrl,
            'domain' => $domain,
            'user_id' => $user->id,
            'stripe_session_id' => $sessionId,
            'paid' => true,
            'status' => $tierSlug === 'scan-basic' ? QuickScan::STATUS_PAID : QuickScan::STATUS_SCANNED,
            'source' => 'checkout_' . $tierSlug,
            'upgrade_plan' => $tierSlug === 'scan-basic' ? null : $tierSlug,
            'upgrade_status' => $tierSlug === 'scan-basic' ? null : 'paid',
            'upgraded_at' => $tierSlug === 'scan-basic' ? null : now(),
        ]);

        Auth::login($user, true);

        // Clear scan entry session data
        Session::forget('scan_entry');

        $user->update(['last_login_at' => now()]);

        FunnelEvent::fire('checkout_completed', userId: $user->id, scanId: $scan->id, metadata: [
            'tier' => $tierSlug,
            'amount_cents' => self::TIERS[$tierSlug]['amount'],
            'is_new_user' => $isNew,
            'stripe_session_id' => $sessionId,
        ]);

        // Dispatch tier-specific funnel email sequence
        SendUpgradeFunnelEmailsJob::dispatch($scan->id, $user->id, $tierSlug);

        Log::info('CheckoutController: unified entry completed', [
            'user_id' => $user->id,
            'tier' => $tierSlug,
            'is_new' => $isNew,
            'scan_id' => $scan->id,
        ]);

        return redirect('/dashboard')->with('system_entry', $systemTier->value);
    }

    /**
     * Build a Stripe Checkout session and redirect.
     * All tiers redirect to /checkout/complete on success.
     */
    private function redirectToStripe(string $tierSlug, Request $request, array $extra = [])
    {
        $tier = self::TIERS[$tierSlug];

        $metadata = [
            'tier' => $tierSlug,
            'ip' => $request->ip(),
            'ref' => $request->query('ref', 'direct'),
        ];

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
