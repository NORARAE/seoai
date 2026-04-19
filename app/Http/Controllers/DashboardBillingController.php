<?php

namespace App\Http\Controllers;

use App\Models\QuickScan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

class DashboardBillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Keep billing history aligned with all scan activity tied to the user's email.
        QuickScan::query()
            ->where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        $sessionIds = $this->collectSessionIds($user->id, $user->stripe_checkout_session_id)
            ->take(30)
            ->values();

        $payments = collect();
        $customerId = null;

        foreach ($sessionIds as $sessionId) {
            try {
                $session = Cashier::stripe()->checkout->sessions->retrieve(
                    $sessionId,
                    ['expand' => ['payment_intent.latest_charge', 'line_items.data.price.product']]
                );

                if (!$customerId && is_string($session->customer ?? null) && $session->customer !== '') {
                    $customerId = $session->customer;
                }

                $lineItems = collect($session->line_items->data ?? []);
                $firstItem = $lineItems->first();
                $receiptUrl = $session->payment_intent->latest_charge->receipt_url ?? null;

                $payments->push([
                    'session_id' => (string) $session->id,
                    'status' => (string) ($session->payment_status ?? 'unknown'),
                    'amount' => (int) ($session->amount_total ?? 0),
                    'currency' => strtoupper((string) ($session->currency ?? 'usd')),
                    'description' => (string) (
                        $firstItem->description
                        ?? $session->metadata->tier
                        ?? 'Access activation'
                    ),
                    'purchased_at' => isset($session->created) ? now()->createFromTimestamp((int) $session->created) : null,
                    'receipt_url' => is_string($receiptUrl) && $receiptUrl !== '' ? $receiptUrl : null,
                ]);
            } catch (\Throwable $e) {
                Log::warning('DashboardBillingController: unable to retrieve checkout session', [
                    'session_id' => $sessionId,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $payments = $payments
            ->sortByDesc(fn(array $payment) => $payment['purchased_at']?->timestamp ?? 0)
            ->values();

        $payments = $payments
            ->values()
            ->map(function (array $payment, int $idx) {
                $activation = $this->deriveActivationMetadata($payment, $idx === 0);

                return array_merge($payment, $activation);
            })
            ->values();

        $portalUrl = null;
        if ($customerId) {
            try {
                $portal = Cashier::stripe()->billingPortal->sessions->create([
                    'customer' => $customerId,
                    'return_url' => route('app.billing'),
                ]);

                $portalUrl = (string) $portal->url;
            } catch (\Throwable $e) {
                Log::warning('DashboardBillingController: unable to create billing portal session', [
                    'user_id' => $user->id,
                    'customer_id' => $customerId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('dashboard.billing', [
            'payments' => $payments,
            'portalUrl' => $portalUrl,
            'supportEmail' => 'hello@seoaico.com',
        ]);
    }

    private function deriveActivationMetadata(array $payment, bool $isMostRecent): array
    {
        $description = strtolower((string) ($payment['description'] ?? ''));
        $status = strtolower((string) ($payment['status'] ?? 'unknown'));

        $type = 'System Activation';
        $layerUnlocked = 'Execution Layer';
        $summary = 'System Extended for implementation depth and sustained visibility control.';
        $nextAction = 'Continue execution from this activation record.';
        $nextActionLabel = 'Open Execution Workspace';
        $nextActionHref = url('/dashboard#ai-scans');
        $sourceType = 'System Upgrade';

        if (str_contains($description, 'quick scan')) {
            $type = 'Quick Scan';
            $layerUnlocked = 'Baseline Layer';
            $summary = 'Baseline Layer Activated with score confirmation and primary bottleneck detection.';
            $nextAction = 'Advance into Signal Expansion to extend diagnostic depth.';
            $nextActionLabel = 'Continue to Signal Expansion';
            $nextActionHref = url('/dashboard#ai-scans');
            $sourceType = 'Quick Scan Flow';
        } elseif (str_contains($description, 'signal expansion') || str_contains($description, 'diagnostic')) {
            $type = 'Signal Expansion';
            $layerUnlocked = 'Signal Layer';
            $summary = 'Signal Layer Activated with expanded issue visibility across critical retrieval signals.';
            $nextAction = 'Prioritize high-impact signal gaps, then progress to Structural Leverage.';
            $nextActionLabel = 'Open Priority Signal Actions';
            $nextActionHref = url('/dashboard#ai-scans');
            $sourceType = 'Layer Upgrade';
        } elseif (str_contains($description, 'structural leverage') || str_contains($description, 'fix strategy')) {
            $type = 'Structural Leverage';
            $layerUnlocked = 'Structural Layer';
            $summary = 'Structural Layer Activated with ranked correction pathways tied to business impact.';
            $nextAction = 'Execute the top structural sequence and evaluate full System Activation.';
            $nextActionLabel = 'Open Structural Sequence';
            $nextActionHref = url('/dashboard#ai-scans');
            $sourceType = 'Layer Upgrade';
        } elseif (str_contains($description, 'system activation') || str_contains($description, 'optimization')) {
            $type = 'System Activation';
            $layerUnlocked = 'System Layer';
            $summary = 'System Layer Activated for competitive positioning and deployment depth.';
            $nextAction = 'Proceed into deployment and managed execution from this activation baseline.';
            $nextActionLabel = 'Start Deployment Path';
            $nextActionHref = url('/book');
            $sourceType = 'System Upgrade';
        }

        $statusBadge = 'PENDING';
        if ($status === 'paid') {
            $statusBadge = $isMostRecent ? 'ACTIVATED' : 'COMPLETED';
        }

        $sessionId = (string) ($payment['session_id'] ?? '');
        $sessionPrefix = $sessionId !== '' ? substr($sessionId, 0, min(strlen($sessionId), 10)) : 'N/A';
        $sessionSuffix = $sessionId !== '' ? substr($sessionId, -4) : 'N/A';
        $sessionRef = $sessionId !== '' ? ($sessionPrefix . '...' . $sessionSuffix) : 'N/A';
        $receiptAvailable = !empty($payment['receipt_url']) ? 'Available' : 'Unavailable';

        return [
            'activation_type' => $type,
            'status_badge' => $statusBadge,
            'system_description' => $summary,
            'layer_unlocked' => $layerUnlocked,
            'next_action' => $nextAction,
            'next_action_label' => $nextActionLabel,
            'next_action_href' => $nextActionHref,
            'source_type' => $sourceType,
            'details_title' => $type . ' Activation Record',
            'amount_label' => $payment['currency'] . ' ' . number_format(($payment['amount'] ?? 0) / 100, 2),
            'timestamp_label' => $payment['purchased_at']?->format('M d, Y g:i A') ?? 'N/A',
            'session_ref' => $sessionRef,
            'receipt_availability' => $receiptAvailable,
            'activation_explanation' => 'Layer Unlocked. System Extended. Your baseline remains intact while deeper execution context is now active.',
            'system_change' => 'New intelligence depth was activated for this layer and merged into your existing system baseline.',
        ];
    }

    private function collectSessionIds(int $userId, ?string $rootSession): Collection
    {
        $scanSessions = QuickScan::query()
            ->where('user_id', $userId)
            ->get(['stripe_session_id', 'upgrade_stripe_session_id'])
            ->flatMap(function (QuickScan $scan) {
                return [
                    $scan->stripe_session_id,
                    $scan->upgrade_stripe_session_id,
                ];
            });

        return collect([$rootSession])
            ->merge($scanSessions)
            ->filter(fn($value) => is_string($value) && trim($value) !== '')
            ->map(fn(string $value) => trim($value))
            ->unique()
            ->values();
    }
}
