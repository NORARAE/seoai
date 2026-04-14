<?php

namespace App\Http\Controllers;

use App\Mail\QuickScanDay1;
use App\Mail\QuickScanDay2;
use App\Mail\QuickScanDay3;
use App\Mail\QuickScanResult;
use App\Models\Lead;
use App\Models\QuickScan;
use App\Services\QuickScanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
     * Validate input, create a pending QuickScan, redirect to Stripe.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url|max:500',
            'email' => 'required|email|max:255',
        ]);

        $url = rtrim($validated['url'], '/');
        $email = strtolower(trim($validated['email']));

        // Normalize URL scheme
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        // Create pending scan record
        $scan = QuickScan::create([
            'email' => $email,
            'url' => $url,
            'status' => QuickScan::STATUS_PENDING,
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
     * Verify payment, run scan, persist results, send email, show results.
     */
    public function result(Request $request, QuickScanService $scanner)
    {
        $sessionId = $request->query('session_id');
        $scanId = $request->query('scan_id');

        if (!$sessionId || !$scanId) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Invalid result link.']);
        }

        $scan = QuickScan::find((int) $scanId);

        if (!$scan) {
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Scan record not found.']);
        }

        // If already scanned, just show results
        if ($scan->status === QuickScan::STATUS_SCANNED && $scan->score !== null) {
            return view('public.quick-scan-result', compact('scan'));
        }

        // Verify payment with Stripe
        try {
            $stripeSession = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

            if ($stripeSession->payment_status !== 'paid') {
                return redirect()->route('quick-scan.show')
                    ->withErrors(['error' => 'Payment not confirmed. Please complete checkout.']);
            }
        } catch (\Throwable $e) {
            Log::error('QuickScan Stripe session retrieval failed', [
                'session_id' => $sessionId,
                'scan_id' => $scanId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('quick-scan.show')
                ->withErrors(['error' => 'Could not verify payment. Contact hello@seoaico.com.']);
        }

        // Mark paid
        $scan->update([
            'paid' => true,
            'stripe_session_id' => $sessionId,
            'status' => QuickScan::STATUS_PAID,
        ]);

        // Run the scan
        $result = $scanner->scan($scan->url);

        $scan->update([
            'score' => $result['score'],
            'issues' => $result['issues'],
            'strengths' => $result['strengths'],
            'fastest_fix' => $result['fastest_fix'],
            'raw_checks' => $result['raw_checks'],
            'status' => QuickScan::STATUS_SCANNED,
        ]);
        $scan->refresh();

        // Upsert CRM lead
        try {
            $lead = Lead::updateOrCreate(
                ['email' => $scan->email],
                [
                    'website' => $scan->url,
                    'source' => 'quick-scan',
                    'lifecycle_stage' => Lead::STAGE_NEW,
                    'score' => $scan->score,
                    'tags' => array_merge(
                        Lead::where('email', $scan->email)->value('tags') ?? [],
                        ['quick-scan:purchased']
                    ),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('QuickScan Lead upsert failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Email 1: Immediate result email
        try {
            Mail::to($scan->email)->queue(new QuickScanResult($scan));
        } catch (\Throwable $e) {
            Log::warning('QuickScan Email 1 failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Email 2: Day 1 follow-up
        try {
            Mail::to($scan->email)->later(now()->addDay(), new QuickScanDay1($scan));
        } catch (\Throwable $e) {
            Log::warning('QuickScan Email 2 (Day 1) schedule failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Email 3: Day 3 deepen
        try {
            Mail::to($scan->email)->later(now()->addDays(3), new QuickScanDay2($scan));
        } catch (\Throwable $e) {
            Log::warning('QuickScan Email 3 (Day 3) schedule failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        // Email 4: Day 5 conversion
        try {
            Mail::to($scan->email)->later(now()->addDays(5), new QuickScanDay3($scan));
        } catch (\Throwable $e) {
            Log::warning('QuickScan Email 4 (Day 5) schedule failed', ['scan_id' => $scan->id, 'error' => $e->getMessage()]);
        }

        return view('public.quick-scan-result', compact('scan'));
    }

    /**
     * GET /quick-scan/cancelled
     * Show a cancelled payment page.
     */
    public function cancelled()
    {
        return view('public.quick-scan-cancelled');
    }
}
