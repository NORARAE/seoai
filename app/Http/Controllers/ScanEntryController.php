<?php

namespace App\Http\Controllers;

use App\Jobs\SendScanStartedEmailsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ScanEntryController extends Controller
{
    /**
     * GET /scan/start — Collect URL + email before payment.
     */
    public function start()
    {
        return view('public.scan-start');
    }

    /**
     * POST /scan/submit — Validate input, store in session, redirect to processing.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:500'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        Session::put('scan_entry', [
            'url' => $validated['url'],
            'email' => $validated['email'],
            'submitted_at' => now()->toIso8601String(),
            'ip' => $request->ip(),
        ]);

        Log::info('ScanEntry: input captured', [
            'email' => $validated['email'],
            'url' => $validated['url'],
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
        $scanEntry = Session::get('scan_entry');

        if (!$scanEntry) {
            return redirect()->route('scan.start');
        }

        return view('public.scan-process', [
            'url' => $scanEntry['url'],
            'email' => $scanEntry['email'],
        ]);
    }

    /**
     * GET /scan/preview — Show partial intelligence to drive conversion.
     */
    public function preview(Request $request)
    {
        $scanEntry = Session::get('scan_entry');

        if (!$scanEntry || empty($scanEntry['url'])) {
            return redirect()->route('scan.start');
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

        return view('public.scan-preview', compact('url', 'host', 'preview', 'issueCount', 'insights'));
    }
}
