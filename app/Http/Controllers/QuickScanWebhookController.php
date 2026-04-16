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

        Log::info('QuickScan webhook: received', ['event_type' => $event->type, 'event_id' => $event->id]);

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

        // ── Handle upgrade payments (idempotent) ─────────────────────────
        $upgradePlan = $session->metadata?->upgrade_plan ?? null;

        if ($upgradePlan && in_array($upgradePlan, ['diagnostic', 'fix-strategy', 'optimization'], true)) {
            if ($scan->upgrade_status !== 'paid') {
                $scan->update([
                    'upgrade_plan' => $upgradePlan,
                    'upgrade_status' => 'paid',
                    'upgrade_stripe_session_id' => $session->id,
                    'upgraded_at' => now(),
                ]);
                Log::info('QuickScan webhook: upgrade payment confirmed', [
                    'scan_id' => $scanId,
                    'plan' => $upgradePlan,
                    'session_id' => $session->id,
                ]);
            }

            return response()->json(['message' => 'Upgrade confirmed.', 'scan_id' => $scanId]);
        }

        // ── Handle initial $2 payment (idempotent) ───────────────────────
        if (!$scan->paid) {
            $scan->update([
                'paid' => true,
                'stripe_session_id' => $session->id,
                'status' => QuickScan::STATUS_PAID,
            ]);
            Log::info('QuickScan webhook: marked paid', ['scan_id' => $scanId]);
        }

        // Always dispatch — the job is fully idempotent:
        //   - skips scan if already STATUS_SCANNED
        //   - skips emails if emails_sent=true
        //   - CRM upsert is updateOrCreate (safe to repeat)
        RunQuickScanJob::dispatch($scan->id);

        Log::info('QuickScan webhook: dispatched RunQuickScanJob', [
            'scan_id' => $scanId,
            'session_id' => $session->id,
            'scan_status' => $scan->status,
            'emails_sent' => $scan->emails_sent,
        ]);

        return response()->json(['message' => 'Scan queued.', 'scan_id' => $scanId]);
    }
}
