<?php

namespace App\Http\Controllers;

use App\Actions\NotifyOwnerOfPurchase;
use App\Jobs\RunQuickScanJob;
use App\Models\FunnelEvent;
use App\Models\QuickScan;
use App\Models\User;
use App\Services\Entitlements\EntitlementService;
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
    public function __construct(private readonly EntitlementService $entitlements)
    {
    }

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
        $scanUrl = trim((string) ($session->metadata?->scan_url ?? ''));
        $scanEmail = strtolower(trim((string) ($session->metadata?->scan_email ?? ($session->customer_details?->email ?? ''))));

        if ($scanId) {
            // ── Flow A: scan_id in metadata (QuickScanController checkout path) ─────
            Log::info('QuickScan webhook: Flow A — resolving by scan_id', [
                'scan_id' => $scanId,
                'session_id' => $session->id,
            ]);

            $scan = QuickScan::find($scanId);

            if (!$scan) {
                Log::error('QuickScan webhook: Flow A — scan not found', [
                    'scan_id' => $scanId,
                    'session_id' => $session->id,
                ]);

                return response()->json(['message' => 'Scan not found.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } elseif ($scanEmail !== '' || $scanUrl !== '') {
            // ── Flow B: scan_url/scan_email metadata (CheckoutController::scanBasic) ─
            Log::info('QuickScan webhook: Flow B — resolving by scan_url/scan_email', [
                'scan_email' => $scanEmail,
                'scan_url' => $scanUrl,
                'session_id' => $session->id,
            ]);

            $scan = $this->resolveOrCreateScanForFlowB($session, $scanEmail, $scanUrl);
        } else {
            // ── Unknown: metadata has neither scan_id nor scan_url/scan_email ────────
            Log::warning('QuickScan webhook: no resolution metadata — cannot process', [
                'session_id' => $session->id,
                'metadata_keys' => array_keys((array) ($session->metadata ?? [])),
            ]);

            // Return 200 so Stripe stops retrying for truly unresolvable events.
            return response()->json(['message' => 'Insufficient metadata to resolve scan.']);
        }

        // Normalise $scanId to the resolved scan for all downstream log/response use.
        $scanId = $scan->id;

        // Recovery safety net: if this scan is orphaned but a user exists by email, attach it.
        if (is_null($scan->user_id) && filled($scan->email)) {
            $matchedUser = User::whereRaw('LOWER(email) = ?', [strtolower((string) $scan->email)])->first();
            if ($matchedUser) {
                $scan->update(['user_id' => $matchedUser->id]);
                $scan->refresh();
            }
        }

        // ── Handle upgrade payments (idempotent) ─────────────────────────
        $upgradePlan = QuickScan::normalizeUpgradePlan($session->metadata?->upgrade_plan ?? null);

        if ($upgradePlan && in_array($upgradePlan, ['diagnostic', 'fix-strategy', 'optimization'], true)) {
            $incomingRank = QuickScan::rankForUpgradePlan($upgradePlan);
            $currentRank = QuickScan::rankForUpgradePlan($scan->normalizedUpgradePlan());

            // Update when payment is not marked paid yet OR when this purchase advances tier rank.
            if ($scan->upgrade_status !== 'paid' || $incomingRank > $currentRank) {
                $scan->update([
                    'upgrade_plan' => $upgradePlan,
                    'upgrade_status' => 'paid',
                    'upgrade_stripe_session_id' => $session->id,
                    'upgraded_at' => now(),
                ]);

                FunnelEvent::fire(FunnelEvent::UPGRADE_PURCHASED, scanId: $scan->id, metadata: [
                    'plan' => (string) $upgradePlan,
                    'source_page' => 'quick_scan_webhook',
                ]);

                FunnelEvent::fire(FunnelEvent::PAYMENT_SUCCESS, scanId: $scan->id, metadata: [
                    'flow' => 'quick_scan_upgrade',
                    'tier' => (string) $upgradePlan,
                    'source_page' => 'quick_scan_webhook',
                ]);

                Log::info('QuickScan webhook: upgrade payment confirmed', [
                    'scan_id' => $scanId,
                    'plan' => $upgradePlan,
                    'session_id' => $session->id,
                ]);

                $this->entitlements->issueForScan($scan->fresh());

                // Notify owner of upgrade purchase
                $upgradeTiers = [
                    'diagnostic' => ['name' => 'Signal Analysis — Full Analysis', 'amount' => 9900],
                    'fix-strategy' => ['name' => 'Action Plan — Fix Strategy', 'amount' => 24900],
                    'optimization' => ['name' => 'Guided Execution — Full Deployment', 'amount' => 48900],
                ];
                $ut = $upgradeTiers[$upgradePlan] ?? ['name' => $upgradePlan, 'amount' => 0];
                (new NotifyOwnerOfPurchase)->execute($scan, $ut['name'], $ut['amount']);
            }

            return response()->json(['message' => 'Upgrade confirmed.', 'scan_id' => $scanId]);
        }

        // ── Handle initial $2 payment (idempotent) ───────────────────────
        if ($scan->stripe_session_id && $scan->stripe_session_id !== $session->id) {
            Log::warning('QuickScan webhook: session mismatch for scan', [
                'scan_id' => $scanId,
                'expected_session' => $scan->stripe_session_id,
                'received_session' => $session->id,
            ]);

            return response()->json(['message' => 'Session mismatch.'], Response::HTTP_CONFLICT);
        }

        if (!$scan->paid) {
            $scan->update([
                'paid' => true,
                'stripe_session_id' => $session->id,
                'status' => QuickScan::STATUS_PAID,
            ]);
            Log::info('QuickScan webhook: marked paid', ['scan_id' => $scanId]);

            $this->entitlements->issueForScan($scan->fresh());

            // Notify owner of $2 scan purchase
            (new NotifyOwnerOfPurchase)->execute($scan, 'AI Citation Quick Scan', 200);
        }

        if ($scan->paid) {
            $this->entitlements->issueForScan($scan->fresh());
        }

        // Always dispatch — the job is fully idempotent:
        //   - skips scan if already STATUS_SCANNED
        //   - skips emails if emails_sent=true
        //   - CRM upsert is updateOrCreate (safe to repeat)
        if (app()->environment('local')) {
            RunQuickScanJob::dispatchSync($scan->id);
        } else {
            RunQuickScanJob::dispatch($scan->id);
        }

        Log::info('QuickScan webhook: dispatched RunQuickScanJob', [
            'scan_id' => $scanId,
            'session_id' => $session->id,
            'scan_status' => $scan->status,
            'emails_sent' => $scan->emails_sent,
        ]);

        return response()->json(['message' => 'Scan queued.', 'scan_id' => $scanId]);
    }

    /**
     * Flow B resolution: no scan_id in metadata.
     * Tries to match an existing QuickScan by Stripe session ID (idempotent),
     * then by email + domain (recent unpaid), otherwise creates a new record.
     */
    private function resolveOrCreateScanForFlowB(object $session, string $email, string $rawUrl): QuickScan
    {
        // 1. Match by stripe_session_id — safe for webhook re-deliveries.
        $existing = QuickScan::where('stripe_session_id', $session->id)->first();
        if ($existing) {
            Log::info('QuickScan webhook: Flow B — matched existing scan by stripe_session_id', [
                'scan_id' => $existing->id,
                'session_id' => $session->id,
            ]);
            return $existing;
        }

        // 2. Match a recent unpaid scan for the same email + domain.
        if ($email !== '' && $rawUrl !== '') {
            $domain = parse_url(rtrim($rawUrl, '/'), PHP_URL_HOST) ?: $rawUrl;

            $existing = QuickScan::where('email', $email)
                ->where('domain', $domain)
                ->where('paid', false)
                ->where('created_at', '>=', now()->subDay())
                ->orderByDesc('created_at')
                ->first();

            if ($existing) {
                Log::info('QuickScan webhook: Flow B — matched recent unpaid scan by email+domain', [
                    'scan_id' => $existing->id,
                    'session_id' => $session->id,
                    'email' => $email,
                    'domain' => $domain,
                ]);
                return $existing;
            }
        }

        // 3. Create a new QuickScan record from the Stripe session data.
        $url = $rawUrl !== '' ? rtrim($rawUrl, '/') : '';
        if ($url !== '' && !preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }
        if ($url === '' && $email !== '') {
            $url = 'https://' . ltrim(substr($email, strpos($email, '@') + 1), 'www.');
        }
        $domain = parse_url($url, PHP_URL_HOST) ?: $url;
        $customerEmail = $email !== '' ? $email : (string) ($session->customer_details?->email ?? '');

        $scan = QuickScan::create([
            'email' => $customerEmail,
            'url' => $url,
            'domain' => $domain,
            'stripe_session_id' => $session->id,
            'paid' => true,
            'status' => QuickScan::STATUS_PAID,
            'source' => 'checkout_scan-basic',
        ]);

        Log::info('QuickScan webhook: Flow B — created new QuickScan', [
            'scan_id' => $scan->id,
            'session_id' => $session->id,
            'email' => $customerEmail,
            'domain' => $domain,
        ]);

        return $scan;
    }
}
