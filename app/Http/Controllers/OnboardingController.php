<?php

namespace App\Http\Controllers;

use App\Mail\OnboardingReceived;
use App\Models\Booking;
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
            return view('public.onboarding-start', [
                'booking' => null,
                'isPreview' => true,
            ]);
        }

        $booking = Booking::with('consultType')
            ->where('id', $bookingId)
            ->whereIn('status', ['confirmed', 'pending'])
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
            'submitted_at' => now(),
        ]);

        // ── Advance CRM status ────────────────────────────────────────────────
        $existingTags = $lead->tags ?? [];
        $lead->update([
            'onboarding_status' => 'submitted',
            'lifecycle_stage' => \App\Models\Lead::STAGE_OPPORTUNITY_IDENTIFIED,
            'tags' => array_values(array_unique(array_merge($existingTags, ['qualified']))),
        ]);

        // ── Send confirmation email ───────────────────────────────────────────
        try {
            Mail::to($lead->email)->queue(new OnboardingReceived($lead, $submission));
        } catch (\Exception $e) {
            Log::channel('booking')->error('Onboarding confirmation email failed', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::channel('booking')->info('Onboarding submitted', [
            'lead_id' => $lead->id,
            'booking_id' => $validated['booking_id'],
            'submission_id' => $submission->id,
        ]);

        return redirect()->route('onboarding.done');
    }

    /**
     * Onboarding thank-you page.
     * GET /onboarding/done
     */
    public function done(Request $request)
    {
        return view('public.onboarding-done', [
            'alreadySubmitted' => $request->session()->get('already_submitted', false),
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
