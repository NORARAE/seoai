<?php

namespace App\Http\Controllers;

use App\Jobs\RunQuickScanJob;
use App\Models\QuickScan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * Handles Stripe webhooks for the Quick Scan payment flow.
 *
 * Endpoint: POST /api/v1/quick-scan/stripe-webhook
 *
 * Covers the case where a customer pays but closes the browser before
 * being redirected to /quick-scan/result. The webhook marks the scan
 * as paid and dispatches RunQuickScanJob to execute the scan + email
 * sequence asynchronously. 100% idempotent.
 *
 * Register in Stripe Dashboard:
 *   https://seoaico.com/api/v1/quick-scan/stripe-webhook
 * Events to send: checkout.session.completed
 */
class QuickScanWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = (string) config('services.stripe_quick_scan.webhook_secret', '');

        // ── Signature verification ────────────────────────────────────────
        if ($secret === '') {
            Log::error('QuickScan Stripe webhook secret is not configured.');

            return response()->json(['message' => 'Webhook secret not configured.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $event = Webhook::constructEvent($payload, (string) $sigHeader, $secret);
        } catch (UnexpectedValueException $e) {
            Log::warning('QuickScan webhook: malformed payload', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Invalid payload.'], Response::HTTP_BAD_REQUEST);
        } catch (SignatureVerificationException $e) {
            Log::warning('QuickScan webhook: invalid signature', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Invalid signature.'], Response::HTTP_BAD_REQUEST);
        }

        // ── Only handle checkout.session.completed ────────────────────────
        if ($event->type !== 'checkout.session.completed') {
            return response()->json(['message' => 'Event type not handled.', 'event' => $event->type]);
        }

        $session = $event->data->object;

        if (($session->payment_status ?? null) !== 'paid') {
            return response()->json(['message' => 'Payment not yet completed.']);
        }

        $scanId = (int) ($session->metadata?->scan_id ?? 0);

        if (!$scanId) {
            Log::warning('QuickScan webhook: missing scan_id in metadata', [
                'session_id' => $session->id,
            ]);

            return response()->json(['message' => 'No scan_id in metadata.']);
        }

        // ── Find and process the scan ─────────────────────────────────────
        $scan = QuickScan::find($scanId);

        if (!$scan) {
            Log::error('QuickScan webhook: scan not found', [
                'scan_id' => $scanId,
                'session_id' => $session->id,
            ]);

            return response()->json(['message' => 'Scan not found.']);
        }

        // Idempotent: already processed by the synchronous result handler.
        if ($scan->status === QuickScan::STATUS_SCANNED) {
            return response()->json(['message' => 'Already scanned.', 'scan_id' => $scanId]);
        }

        // Mark paid (idempotent — may already be paid from result handler)
        if (!$scan->paid) {
            $scan->update([
                'paid' => true,
                'stripe_session_id' => $session->id,
                'status' => QuickScan::STATUS_PAID,
            ]);
        }

        // Dispatch scan + email sequence
        RunQuickScanJob::dispatch($scan->id);

        Log::info('QuickScan webhook: dispatched RunQuickScanJob', [
            'scan_id' => $scanId,
            'session_id' => $session->id,
        ]);

        return response()->json(['message' => 'Scan queued.', 'scan_id' => $scanId]);
    }
}
