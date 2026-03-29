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

        $booking = Booking::with('consultType')
            ->where('id', $bookingId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->firstOrFail();

        // If they already submitted, send them to done
        $existing = OnboardingSubmission::whereHas(
            'lead', fn ($q) => $q->where('booking_id', $booking->id)
        )->first();

        if ($existing) {
            return redirect()->route('onboarding.done')
                ->with('already_submitted', true);
        }

        return view('public.onboarding-start', compact('booking'));
    }

    /**
     * Handle onboarding form submission.
     * POST /onboarding/submit
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_id'           => 'required|integer|exists:bookings,id',
            'business_name'        => 'required|string|max:255',
            'website'              => 'nullable|url|max:500',
            'service_area'         => 'nullable|string|max:1000',
            'license'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'primary_contact'      => 'required|string|max:255',
            'phone'                => 'required|string|max:50',
            'ad_budget_ready'      => 'required|in:0,1',
            'payment_method_for_ads' => 'nullable|string|max:255',
        ], [
            'license.required'     => 'Business license upload is required.',
            'license.mimes'        => 'License must be a PDF, JPG, or PNG file.',
            'license.max'          => 'License file must be under 5 MB.',
            'ad_budget_ready.required' => 'Please indicate your ad budget readiness.',
        ]);

        // Verify booking belongs to an existing lead (lead created at booking time)
        $lead = Lead::where('booking_id', $validated['booking_id'])
            ->firstOrFail();

        // Block duplicate submissions
        if (OnboardingSubmission::where('lead_id', $lead->id)->exists()) {
            return redirect()->route('onboarding.done')
                ->with('already_submitted', true);
        }

        // ── Secure file storage ───────────────────────────────────────────────
        // Files go to storage/app/private/onboarding/{booking_id}/ — never public.
        $file = $request->file('license');
        $ext  = strtolower($file->getClientOriginalExtension());

        // Unpredictable filename — prevents enumeration
        $storedName = Str::random(32) . '.' . $ext;
        $storagePath = 'onboarding/' . $validated['booking_id'] . '/' . $storedName;

        Storage::disk('local')->put(
            $storagePath,
            file_get_contents($file->getRealPath())
        );

        // ── Create submission record ──────────────────────────────────────────
        $submission = OnboardingSubmission::create([
            'lead_id'                => $lead->id,
            'booking_id'             => $validated['booking_id'],
            'business_name'          => $validated['business_name'],
            'website'                => $validated['website'],
            'service_area'           => $validated['service_area'],
            'license_path'           => $storagePath,
            'license_original_name'  => $file->getClientOriginalName(),
            'license_size_bytes'     => $file->getSize(),
            'license_mime'           => $file->getMimeType(),
            'primary_contact'        => $validated['primary_contact'],
            'phone'                  => $validated['phone'],
            'ad_budget_ready'        => (bool) $validated['ad_budget_ready'],
            'payment_method_for_ads' => $validated['payment_method_for_ads'],
            'submitted_at'           => now(),
        ]);

        // ── Advance CRM status ────────────────────────────────────────────────
        $lead->update([
            'onboarding_status' => 'submitted',
            'lifecycle_stage'   => \App\Models\Lead::STAGE_ONBOARDING_SUBMITTED,
        ]);

        // ── Send confirmation email ───────────────────────────────────────────
        try {
            Mail::to($lead->email)->queue(new OnboardingReceived($lead, $submission));
        } catch (\Exception $e) {
            Log::channel('booking')->error('Onboarding confirmation email failed', [
                'lead_id' => $lead->id,
                'error'   => $e->getMessage(),
            ]);
        }

        Log::channel('booking')->info('Onboarding submitted', [
            'lead_id'       => $lead->id,
            'booking_id'    => $validated['booking_id'],
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

        abort_if(! $submission->license_path, 404, 'No license file on record.');
        abort_if(
            ! Storage::disk('local')->exists($submission->license_path),
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
