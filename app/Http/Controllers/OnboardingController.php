<?php

namespace App\Http\Controllers;

use App\Jobs\SendOnboardingFollowUpJob;
use App\Mail\OnboardingReceived;
use App\Models\Booking;
use App\Models\FunnelEvent;
use App\Models\Lead;
use App\Models\OnboardingSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding intake form.
     * GET /onboarding/start?booking={id}
     */
    public function start(Request $request)
    {
        $bookingId = (int) $request->query('booking', 0);

        // Preview mode — no booking required
        if ($bookingId === 0) {
            FunnelEvent::fire(FunnelEvent::ONBOARDING_STARTED);
            return view('public.onboarding-start', [
                'booking' => null,
                'isPreview' => true,
            ]);
        }

        $booking = Booking::with('consultType')
            ->where('id', $bookingId)
            ->whereIn('status', ['confirmed', 'pending', 'awaiting_payment'])
            ->firstOrFail();

        // If they already submitted, send them to done
        $existing = OnboardingSubmission::whereHas(
            'lead',
            fn($q) => $q->where('booking_id', $booking->id)
        )->first();

        if ($existing) {
            return redirect()->route('onboarding.done')
                ->with('already_submitted', true);
        }

        FunnelEvent::fire(FunnelEvent::ONBOARDING_STARTED, null, null, ['booking_id' => $booking->id]);
        return view('public.onboarding-start', [
            'booking' => $booking,
            'isPreview' => false,
        ]);
    }

    /**
     * Handle onboarding form submission.
     * POST /onboarding/submit
     */
    public function submit(Request $request): RedirectResponse
    {
        // Honeypot — bots fill hidden fields; legitimate users never do
        if ($request->filled('website_confirm')) {
            return redirect()->route('onboarding.done');
        }

        $validated = $request->validate([
            'booking_id' => 'nullable|integer|exists:bookings,id',
            'email' => 'nullable|email|max:255',
            'business_name' => 'required|string|max:255',
            'website' => 'nullable|string|max:500',
            'service_area' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:2000',
            'challenges' => 'nullable|string|max:2000',
            'growth_intent' => 'nullable|in:aggressive,steady,unsure',
            'ads_status' => 'nullable|in:running,has_budget,no_budget,not_interested',
            'license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'primary_contact' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'ad_budget_ready' => 'nullable|in:0,1',
            'payment_method_for_ads' => 'nullable|string|max:255',
            'analytics_access' => 'nullable|in:0,1',
            'search_console_access' => 'nullable|in:0,1',
            'platform_type' => 'nullable|in:wordpress,shopify,other',
            'access_method' => 'nullable|in:invite_email,provide_later,need_help',
            'add_ons' => 'nullable|array|max:10',
            'add_ons.*' => 'string|max:100',
            'rd_referral_interest' => 'nullable|boolean',
            'lead_type' => 'nullable|in:single_location,multi_location,agency',
            'number_of_locations' => 'nullable|in:1,2_to_5,6_to_10,11_to_20,20_plus',
            'commitment_length' => 'nullable|in:4_month,3_month,2_month',
            'payment_structure' => 'nullable|in:full_prepay,50_50_split,activation_plus_subscription',
        ], [
            'license.mimes' => 'License must be a PDF, JPG, or PNG file.',
            'license.max' => 'License file must be under 5 MB.',
        ]);

        $isPreview = empty($validated['booking_id']);

        if ($isPreview) {
            // Preview mode — create lead directly from form data
            $request->validate(['email' => 'required|email|max:255']);
            $lead = Lead::create([
                'booking_id' => null,
                'name' => $validated['primary_contact'],
                'email' => $validated['email'],
                'company' => $validated['business_name'],
                'website' => $validated['website'] ?? null,
                'phone' => $validated['phone'],
                'session_type' => 'preview',
                'payment_status' => 'none',
                'source' => 'preview',
                'lifecycle_stage' => Lead::STAGE_NEW,
            ]);
        } else {
            // Verify booking belongs to an existing lead (lead created at booking time)
            $lead = Lead::where('booking_id', $validated['booking_id'])
                ->firstOrFail();
        }

        // Block duplicate submissions
        if (OnboardingSubmission::where('lead_id', $lead->id)->exists()) {
            return redirect()->route('onboarding.done')
                ->with('already_submitted', true);
        }

        // ── Secure file storage (optional) ───────────────────────────────────
        $storagePath = null;
        $origName = null;
        $fileSize = null;
        $fileMime = null;

        if ($request->hasFile('license') && $request->file('license')->isValid()) {
            $file = $request->file('license');
            $ext = strtolower($file->getClientOriginalExtension());
            $storedName = Str::random(32) . '.' . $ext;
            $folderKey = $validated['booking_id'] ?? ('preview-' . $lead->id);
            $storagePath = 'licenses/' . $folderKey . '/' . $storedName;

            Storage::disk('local')->put(
                $storagePath,
                file_get_contents($file->getRealPath())
            );

            $origName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileMime = $file->getMimeType();
        }

        // ── Create submission record ──────────────────────────────────────────
        $submission = OnboardingSubmission::create([
            'lead_id' => $lead->id,
            'booking_id' => $validated['booking_id'] ?? null,
            'business_name' => $validated['business_name'],
            'website' => $validated['website'] ?? null,
            'service_area' => $validated['service_area'] ?? null,
            'goals' => $validated['goals'] ?? null,
            'challenges' => $validated['challenges'] ?? null,
            'growth_intent' => $validated['growth_intent'] ?? null,
            'ads_status' => $validated['ads_status'] ?? null,
            'license_path' => $storagePath,
            'license_original_name' => $origName,
            'license_size_bytes' => $fileSize,
            'license_mime' => $fileMime,
            'primary_contact' => $validated['primary_contact'],
            'phone' => $validated['phone'] ?? null,
            'ad_budget_ready' => (bool) ($validated['ad_budget_ready'] ?? false),
            'payment_method_for_ads' => $validated['payment_method_for_ads'] ?? null,
            'analytics_access' => (bool) ($validated['analytics_access'] ?? false),
            'search_console_access' => (bool) ($validated['search_console_access'] ?? false),
            'platform_type' => $validated['platform_type'] ?? null,
            'access_method' => $validated['access_method'] ?? null,
            'add_ons' => $validated['add_ons'] ?? null,
            'rd_referral_interest' => (bool) ($validated['rd_referral_interest'] ?? false),
            'lead_type' => $validated['lead_type'] ?? null,
            'number_of_locations' => $validated['number_of_locations'] ?? null,
            // Commitment & routing — inferred from lead_type if not explicitly submitted
            'commitment_length' => $validated['commitment_length'] ?? '4_month',
            'payment_structure' => $validated['payment_structure'] ?? 'full_prepay',
            'offer_path' => match ($validated['lead_type'] ?? null) {
                'single_location' => 'core',
                'multi_location' => 'multi_market',
                'agency' => 'agency',
                default => 'core',
            },
            'rollout_scope' => match ($validated['lead_type'] ?? null) {
                'single_location' => 'single',
                'multi_location' => 'multi',
                'agency' => 'enterprise',
                default => 'single',
            },
            'agency_review_required' => ($validated['lead_type'] ?? null) === 'agency',
            'ads_management_required' => in_array(
                $validated['ads_status'] ?? null,
                ['running', 'has_budget']
            ) || in_array('google_ads_setup', $validated['add_ons'] ?? []),
            'ads_account_control' => 'not_configured',
            'submitted_at' => now(),
        ]);

        // ── Advance CRM status ────────────────────────────────────────────────
        $existingTags = $lead->tags ?? [];
        $lead->update([
            'onboarding_status' => 'submitted',
            'lifecycle_stage' => \App\Models\Lead::STAGE_OPPORTUNITY_IDENTIFIED,
            'tags' => array_values(array_unique(array_merge($existingTags, ['qualified']))),
        ]);

        // Funnel tracking
        FunnelEvent::fire(FunnelEvent::ONBOARDING_COMPLETED, null, $lead->id, ['submission_id' => $submission->id]);

        // ── Send confirmation email ───────────────────────────────────────────
        try {
            Mail::to($lead->email)->queue(new OnboardingReceived($lead, $submission));
        } catch (\Exception $e) {
            Log::channel('booking')->error('Onboarding confirmation email failed', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }

        // ── Queue follow-up emails (+24h and +48h) ────────────────────────────
        SendOnboardingFollowUpJob::dispatch($lead->id, $submission->id, 2)
            ->delay(now()->addHours(24));

        SendOnboardingFollowUpJob::dispatch($lead->id, $submission->id, 3)
            ->delay(now()->addHours(48));

        Log::channel('booking')->info('Onboarding submitted', [
            'lead_id' => $lead->id,
            'booking_id' => $validated['booking_id'],
            'submission_id' => $submission->id,
        ]);

        return redirect()->route('onboarding.done')
            ->with('lead_type', $validated['lead_type'] ?? 'single_location');
    }

    /**
     * Onboarding thank-you page.
     * GET /onboarding/done
     */
    public function done(Request $request)
    {
        return view('public.onboarding-done', [
            'alreadySubmitted' => $request->session()->get('already_submitted', false),
            'leadType' => $request->session()->get('lead_type', 'single_location'),
        ]);
    }

    /**
     * Secure admin-only download of business license files.
     * GET /onboarding/license/{submission}
     * Requires auth. File is never served via a public URL.
     */
    public function downloadLicense(OnboardingSubmission $submission)
    {
        // Only authenticated admins may download
        abort_unless(
            auth()->check() && auth()->user()->canApproveUsers(),
            403
        );

        abort_if(!$submission->license_path, 404, 'No license file on record.');
        abort_if(
            !Storage::disk('local')->exists($submission->license_path),
            404,
            'License file not found in storage.'
        );

        $displayName = $submission->license_original_name
            ?? ('license_' . $submission->id . '.bin');

        return Storage::disk('local')->download(
            $submission->license_path,
            $displayName
        );
    }
}
