<?php

namespace App\Http\Controllers;

use App\Jobs\RunQuickScanJob;
use App\Models\QuickScan;
use App\Services\QuickScanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        // Normalize: prepend https:// if user entered bare domain
        $rawUrl = trim($request->input('url', ''));
        if ($rawUrl !== '' && !preg_match('#^https?://#i', $rawUrl)) {
            $request->merge(['url' => 'https://' . $rawUrl]);
        }

        $validated = $request->validate([
            'url' => ['required', 'url', 'max:500'],
            'email' => ['required', 'email', 'max:255'],
        ], [
            'url.required' => 'Enter a valid website address, such as yoursite.com',
            'url.url' => 'Enter a valid website address, such as yoursite.com',
        ]);

        $url = rtrim($validated['url'], '/');
        $email = strtolower(trim($validated['email']));

        // Create pending scan record (preserve original input)
        $scan = QuickScan::create([
            'email' => $email,
            'url' => $url,
            'url_input' => $rawUrl,
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
     * Verify payment, dispatch scan job, show results.
     * The webhook provides a safety net if the user never reaches this page.
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

        // Run scan synchronously so the user sees results immediately.
        // RunQuickScanJob handles CRM + email drip (idempotent).
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

        // Dispatch job for CRM upsert + email sequence (idempotent — will
        // see STATUS_SCANNED and skip the scan, then run CRM + emails).
        RunQuickScanJob::dispatch($scan->id);

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
