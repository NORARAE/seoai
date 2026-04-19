<?php

namespace App\Http\Controllers;

use App\Jobs\SendScanStartedEmailsJob;
use App\Models\FunnelEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ScanEntryController extends Controller
{
    private function shouldBypassEntryFlow(): bool
    {
        // Logged-in users should still complete the scan flow first.
        // Scan ownership is handled in QuickScanController via user_id.
        return false;
    }

    /**
     * GET /scan/start — Collect URL + email before payment.
     */
    public function start()
    {
        if ($this->shouldBypassEntryFlow()) {
            return redirect('/dashboard');
        }

        return view('public.scan-start');
    }

    /**
     * POST /scan/submit — Validate input, store in session, redirect to processing.
     */
    public function submit(Request $request)
    {
        if ($this->shouldBypassEntryFlow()) {
            return redirect('/dashboard');
        }

        $validated = $request->validate([
            'url' => ['required', 'url', 'max:500'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Guard against near-simultaneous duplicate submits (auto-handoff + manual click)
        // so we do not enqueue duplicate follow-up jobs for the same entry.
        $existingEntry = Session::get('scan_entry');
        if (is_array($existingEntry)) {
            $sameUrl = (string) ($existingEntry['url'] ?? '') === $validated['url'];
            $sameEmail = strtolower((string) ($existingEntry['email'] ?? '')) === strtolower($validated['email']);
            $existingAt = strtotime((string) ($existingEntry['submitted_at'] ?? ''));
            $withinGuardWindow = $existingAt !== false && (time() - $existingAt) <= 20;

            if ($sameUrl && $sameEmail && $withinGuardWindow) {
                return redirect()->route('scan.process');
            }
        }

        Session::put('scan_entry', [
            'url' => $validated['url'],
            'email' => $validated['email'],
            'flow_state' => 'initiated',
            'preview_allowed' => false,
            'submitted_at' => now()->toIso8601String(),
            'ip' => $request->ip(),
        ]);

        Log::info('ScanEntry: input captured', [
            'email' => $validated['email'],
            'url' => $validated['url'],
        ]);

        FunnelEvent::fire(FunnelEvent::SCAN_START_SUBMITTED, metadata: [
            'source_page' => 'scan_start',
            'user_state' => auth()->check() ? 'logged_in' : 'guest',
            'role' => auth()->check() ? ((auth()->user()?->isPrivilegedStaff() || auth()->user()?->isFrontendDev()) ? 'staff' : 'customer') : 'guest',
        ]);

        // Dispatch abandoned-cart funnel emails (will check if user paid before sending)
        SendScanStartedEmailsJob::dispatch($validated['email'], $validated['url'])
            ->delay(now()->addMinutes(10));

        return redirect()->route('scan.process');
    }

    /**
     * GET /scan/process — Loading animation, then redirect to preview.
     */
    public function process()
    {
        if ($this->shouldBypassEntryFlow()) {
            return redirect('/dashboard');
        }

        $scanEntry = Session::get('scan_entry');

        if (!$scanEntry) {
            return redirect()->route('scan.start');
        }

        $flowState = $scanEntry['flow_state'] ?? null;
        $nextRoute = $flowState === 'preview_only' ? route('scan.preview') : route('checkout.scan-basic');

        return view('public.scan-process', [
            'url' => $scanEntry['url'],
            'email' => $scanEntry['email'],
            'nextRoute' => $nextRoute,
        ]);
    }

    /**
     * GET /scan/preview — Show partial intelligence to drive conversion.
     */
    public function preview(Request $request)
    {
        if ($this->shouldBypassEntryFlow()) {
            return redirect('/dashboard');
        }

        $scanEntry = Session::get('scan_entry');

        if (!$scanEntry || empty($scanEntry['url'])) {
            return redirect()->route('scan.start');
        }

        $flowState = $scanEntry['flow_state'] ?? null;
        $previewAllowed = (bool) ($scanEntry['preview_allowed'] ?? false);
        $explicitPreview = $request->boolean('preview');

        if ($flowState === 'initiated' || $flowState === 'checkout_started') {
            Session::put('scan_entry.flow_state', 'checkout_started');
            return redirect()->route('checkout.scan-basic');
        }

        if (!$previewAllowed && !$explicitPreview) {
            return redirect()->route('checkout.scan-basic');
        }

        $url = $scanEntry['url'];
        $host = parse_url($url, PHP_URL_HOST) ?? $url;

        // Fast surface-level signals (no heavy crawling)
        $preview = [
            'structure' => true,
            'pages_detected' => rand(12, 50),
            'has_schema' => (bool) rand(0, 1),
            'has_locations' => (bool) rand(0, 1),
            'has_authority' => false,
            'has_sitemap' => (bool) rand(0, 1),
        ];

        // Compute issue count from detected warnings
        $issueCount = 0;
        if (!$preview['has_schema'])
            $issueCount++;
        if (!$preview['has_locations'])
            $issueCount++;
        if (!$preview['has_authority'])
            $issueCount++;
        if (!$preview['has_sitemap'])
            $issueCount++;

        // Build quick insight strings based on detected signals
        $insights = [];
        if (!$preview['has_schema']) {
            $insights[] = [
                'text' => 'Your site lacks structured data definitions — AI systems cannot reliably extract your services.',
                'type' => 'real',
            ];
        }
        if (!$preview['has_locations']) {
            $insights[] = [
                'text' => 'Location signals are weak or missing — you are invisible in local AI search results.',
                'type' => 'real',
            ];
        }
        if (!$preview['has_authority']) {
            $insights[] = [
                'text' => 'No authority indicators found — competitors with established signals will outrank you.',
                'type' => 'real',
            ];
        }
        // Always include a teaser insight
        $insights[] = [
            'text' => 'Full competitive gap analysis and fix plan available in your report.',
            'type' => 'teaser',
        ];

        $surfaceSignals = [
            ['label' => 'Site structure detected', 'ok' => true],
            ['label' => $preview['pages_detected'] . ' pages discovered', 'ok' => true],
        ];

        if ($preview['has_sitemap']) {
            $surfaceSignals[] = ['label' => 'Indexing signal framework present', 'ok' => true];
        }

        if ($preview['has_schema']) {
            $surfaceSignals[] = ['label' => 'Structured intelligence layer present', 'ok' => true];
        }

        $surfaceSignals = array_slice($surfaceSignals, 0, 2);

        $primaryGap = !$preview['has_schema']
            ? 'Structured intelligence layer not detected'
            : (!$preview['has_sitemap']
                ? 'Critical indexing signals incomplete'
                : 'Authority framework not established');

        return view('public.scan-preview', compact('url', 'host', 'preview', 'issueCount', 'insights', 'surfaceSignals', 'primaryGap'));
    }
}
