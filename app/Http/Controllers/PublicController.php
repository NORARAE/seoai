<?php

namespace App\Http\Controllers;

use App\Mail\NewAccountInquiryAdminMail;
use App\Mail\NewAccountInquiryWelcomeMail;
use App\Models\Inquiry;
use App\Models\SpamLog;
use App\Services\InquiryEnrichmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function __construct(
        private readonly InquiryEnrichmentService $enrichment,
    ) {
    }

    /**
     * Show the public landing page
     */
    public function landing(): View
    {
        $consultTypes = \App\Models\ConsultType::active()->get()->keyBy('slug');
        $types = $consultTypes->values();
        $availableDays = \App\Models\BookingAvailability::active()->pluck('day_of_week')->toArray();
        return view('public.landing', compact('consultTypes', 'types', 'availableDays'));
    }

    /**
     * Handle the licensing enquiry form submission.
     *
     * Flow:
     *  1. Validate form fields (Laravel validation — returns 422 with errors on failure)
     *  2. Honeypot check  — silently reject bots that fill hidden fields
     *  3. Duplicate check — silently suppress repeat submissions within 10 min
     *  4. Persist inquiry (always — even if enrichment or mail fails)
     *  5. Run enrichment pipeline (geo, URL, email, company, reCAPTCHA, timer)
     *  6. Score spam risk — update record
     *  7. Silent-reject high-risk / honeypot hits — log to spam_logs, no admin email
     *  8. Send emails for legitimate submissions
     */
    public function storeLicensingInquiry(Request $request): RedirectResponse
    {
        $successMsg = 'Your enquiry has been submitted. We review every application individually and will be in touch within 1–2 business days.';

        // ── 1. Validate ────────────────────────────────────────────────────────
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'type' => ['required', 'in:agency,business,both'],
            'tier' => ['required', 'in:starter,5k,10k,legacy'],
            'niche' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            // Spam signals — present in form but not stored on model
            // Honeypot — allow any value through; step 2 detects a fill and silently rejects
            'website_confirm' => ['nullable', 'string', 'max:512'],
            'form_loaded_at' => ['nullable', 'integer'],
            'g-recaptcha-response' => ['nullable', 'string', 'max:2048'],
        ]);

        $ip = $request->ip() ?? '0.0.0.0';

        // ── 2. Honeypot ────────────────────────────────────────────────────────
        $honeypotTriggered = !empty($validated['website_confirm']);

        // Also check legacy honeypot field name if still present in request
        if (!$honeypotTriggered && $request->filled('website_url')) {
            $honeypotTriggered = true;
        }

        // ── 3. Duplicate suppression ────────────────────────────────────────────
        $emailKey = 'inquiry_email:' . md5(strtolower($validated['email']));
        $ipKey = 'inquiry_ip:' . md5($ip);

        $isDuplicate = Cache::has($emailKey) || Cache::has($ipKey);

        // ── 4. Persist inquiry (always — even rejections are stored for review) ──
        $formLoadedAt = isset($validated['form_loaded_at']) ? (int) $validated['form_loaded_at'] : null;
        $recaptchaToken = $validated['g-recaptcha-response'] ?? '';

        $inquiry = Inquiry::create([
            'name' => $validated['name'],
            'company' => $validated['company'],
            'email' => $validated['email'],
            'website' => $validated['website'] ?? null,
            'type' => $validated['type'],
            'tier' => $validated['tier'],
            'niche' => $validated['niche'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $ip,
            'status' => 'new',
            'honeypot_triggered' => $honeypotTriggered,
        ]);

        // Mark duplicate caches so next submission in the window is suppressed
        if (!$isDuplicate) {
            Cache::put($emailKey, 1, now()->addMinutes(10));
            Cache::put($ipKey, 1, now()->addMinutes(10));
        }

        // ── 5. Enrichment pipeline ──────────────────────────────────────────────
        try {
            $enrichData = $this->enrichment->enrichAll(
                $ip,
                $validated['email'],
                $validated['website'] ?? null,
                $formLoadedAt,
            );

            // reCAPTCHA (needs token — handled separately from enrichAll)
            $recaptchaScore = null;
            if ($recaptchaToken !== '') {
                $recaptchaScore = $this->enrichment->verifyRecaptcha($recaptchaToken, $ip);
            }
            $enrichData['recaptcha_score'] = $recaptchaScore;

            $inquiry->update($enrichData);
        } catch (\Throwable $e) {
            // Enrichment failure must NOT stop form processing
            Log::error('InquiryEnrichment: pipeline exception (non-fatal)', [
                'inquiry_id' => $inquiry->id,
                'error' => $e->getMessage(),
            ]);
            $enrichData = [];
        }

        // ── 6. Spam risk scoring ────────────────────────────────────────────────
        $enrichData['honeypot_triggered'] = $honeypotTriggered;

        $riskResult = $this->enrichment->scoreSpamRisk($enrichData);
        $spamRisk = $riskResult['spam_risk'];
        $riskScore = $riskResult['_score'];
        $signals = $riskResult['_signals'];

        $inquiry->update(['spam_risk' => $spamRisk]);

        // ── 7. Silent rejection ─────────────────────────────────────────────────
        $shouldReject = $honeypotTriggered
            || $spamRisk === 'high'
            || $isDuplicate;

        if ($shouldReject) {
            $reason = $honeypotTriggered ? 'honeypot_triggered'
                : ($isDuplicate ? 'duplicate_submission' : 'high_risk_score');

            $inquiry->update(['status' => 'rejected']);

            SpamLog::create([
                'inquiry_id' => $inquiry->id,
                'reason' => $reason,
                'spam_risk' => $spamRisk,
                'risk_score' => $riskScore,
                'ip_address' => $ip,
                'email' => $validated['email'],
                'signals' => $signals,
            ]);

            Log::info('Inquiry silently rejected', [
                'id' => $inquiry->id,
                'reason' => $reason,
                'risk' => $spamRisk,
                'score' => $riskScore,
                'signals' => $signals,
                'email' => $validated['email'],
                'ip' => $ip,
            ]);

            // Return normal success — do NOT reveal rejection to submitter
            return redirect(url('/') . '#contact')->with('inquiry_success', $successMsg);
        }

        // ── 8. Send emails ──────────────────────────────────────────────────────
        try {
            Mail::to($inquiry->email)->queue(new NewAccountInquiryWelcomeMail($inquiry));
            $inquiry->update(['welcome_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Failed to queue welcome email', ['inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
        }

        try {
            $adminEmail = config('services.inquiry.recipient_email', 'hello@seoaico.com');
            Mail::to($adminEmail)->queue(new NewAccountInquiryAdminMail($inquiry));
            $inquiry->update(['admin_notified_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Failed to queue admin notification', ['inquiry_id' => $inquiry->id, 'error' => $e->getMessage()]);
        }

        Log::info('Licensing inquiry accepted and emails queued', [
            'id' => $inquiry->id,
            'company' => $inquiry->company,
            'email' => $inquiry->email,
            'tier' => $inquiry->tier,
            'spam_risk' => $spamRisk,
        ]);

        return redirect(url('/') . '#contact')->with('inquiry_success', $successMsg);
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }

    public function howItWorks(): View
    {
        return view('public.how-it-works');
    }

    public function solutions(): View
    {
        return view('public.solutions');
    }

    public function solutionsAgencies(): View
    {
        return view('public.solutions-agencies');
    }

    public function solutionsBusinessOwners(): View
    {
        return view('public.solutions-business-owners');
    }
}
