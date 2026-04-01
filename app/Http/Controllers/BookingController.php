<?php

namespace App\Http\Controllers;

use App\Mail\BookingAlert;
use App\Mail\BookingConfirmed;
use App\Mail\BookingFollowUp;
use App\Mail\BookingPreCall;
use App\Models\Booking;
use App\Models\BookingAvailability;
use App\Models\ConsultType;
use App\Models\Lead;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;

class BookingController extends Controller
{
    public function index()
    {
        $types = ConsultType::active()->get();
        $availableDays = BookingAvailability::active()->pluck('day_of_week')->toArray();

        return view('public.book', compact('types', 'availableDays'));
    }

    /**
     * Return available time slots for a date + consult type (AJAX).
     */
    public function getSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'consult_type_id' => 'required|exists:consult_types,id',
        ]);

        $date = Carbon::parse($request->date);
        $type = ConsultType::findOrFail($request->consult_type_id);

        // Check if this day has availability configured
        $availability = BookingAvailability::active()
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (!$availability) {
            return response()->json(['slots' => [], 'message' => 'No availability on this day.']);
        }

        if (config('services.google.calendar_enabled', false)) {
            try {
                $calendarService = app(GoogleCalendarService::class);
                $slots = $calendarService->getAvailableSlots($date, $type->duration_minutes);
            } catch (\Exception $e) {
                Log::channel('booking')->warning('Calendar unavailable, falling back to DB-only slots', [
                    'error' => $e->getMessage(),
                ]);
                $slots = $this->getFallbackSlots($date, $type->duration_minutes);
            }
        } else {
            $slots = $this->getFallbackSlots($date, $type->duration_minutes);
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consult_type_id' => 'required|exists:consult_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:500',
            'message' => 'nullable|string|max:2000',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
        ]);

        $type = ConsultType::findOrFail($validated['consult_type_id']);
        $date = Carbon::parse($validated['preferred_date']);

        // Race condition guard: check slot still available
        // whereDate() works correctly on both MySQL (DATE column) and SQLite (text column).
        $existing = Booking::whereDate('preferred_date', $date->toDateString())
            ->where('preferred_time', $validated['preferred_time'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'That time slot was just taken. Please pick another.',
            ], 422);
        }

        $booking = Booking::create([
            ...$validated,
            'status' => 'pending',
            'public_booking_token' => Str::random(64),
        ]);

        // Attempt Google Calendar integration
        if (config('services.google.calendar_enabled', false)) {
            try {
                $calendarService = app(GoogleCalendarService::class);
                $result = $calendarService->createBookingEvent($booking);

                $booking->update([
                    'google_event_id' => $result['event_id'],
                    'google_meet_link' => $result['meet_link'],
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::channel('booking')->error('Google Calendar event creation failed — booking saved as pending', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
                // Booking stays as 'pending' for manual review
            }
        }

        $booking->refresh()->load('consultType');

        // ── CRM: create or update lead record ────────────────────────────────
        try {
            Lead::syncFromBooking(
                $booking,
                $booking->consultType->is_free ? 'free' : null,
                \App\Models\Lead::STAGE_BOOKED
            );
        } catch (\Exception $e) {
            Log::channel('booking')->error('Lead sync failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send confirmation emails
        try {
            Mail::to($booking->email)->queue(new BookingConfirmed($booking));
            Mail::to(config('services.booking.owner_email', 'hello@seoaico.com'))
                ->queue(new BookingAlert($booking));
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Schedule pre-call primer 24 h before the session
        try {
            $sessionDateTime = Carbon::parse(
                $booking->preferred_date->toDateString() . ' ' . $booking->preferred_time
            );
            $preCallDelay = $sessionDateTime->copy()->subHours(24);
            if ($preCallDelay->isFuture()) {
                Mail::to($booking->email)
                    ->later($preCallDelay, new BookingPreCall($booking));
            }
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Pre-call email scheduling failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'consult_type' => $booking->consultType->name,
                'date' => $booking->preferred_date->format('l, F j, Y'),
                'time' => Carbon::parse($booking->preferred_time)->format('g:i A'),
                'meet_link' => $booking->google_meet_link,
                'duration' => $booking->consultType->duration_minutes,
            ],
        ]);
    }

    /**
     * Post-booking confirmation landing page.
     * Reached via redirect after successful free OR paid booking.
     */
    public function confirmed(Request $request)
    {
        $booking = Booking::with('consultType')
            ->where('id', (int) $request->query('booking'))
            ->whereIn('status', ['confirmed', 'pending', 'awaiting_payment'])
            ->firstOrFail();

        return view('public.booking-confirmed', compact('booking'));
    }

    /**
     * Upgrade / prep page — shown after payment before full confirmation.
     * Landing → /book → checkout → /book/upgrade → /book/confirmed
     */
    public function upgrade(Request $request)
    {
        $booking = Booking::with('consultType')
            ->where('id', (int) $request->query('booking'))
            ->whereIn('status', ['confirmed', 'pending', 'awaiting_payment'])
            ->firstOrFail();

        return view('public.book-upgrade', compact('booking'));
    }

    /**
     * Initiate a Stripe Checkout session for a paid consult booking.
     * Creates the booking in awaiting_payment status and returns the checkout URL.
     */
    public function initiateCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consult_type_id' => 'required|exists:consult_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:500',
            'message' => 'nullable|string|max:2000',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'add_ons' => 'nullable|array|max:4',
            'add_ons.*' => 'string|in:seo_audit,competitor_analysis,thirty_day_plan,strategy_followup',
        ]);

        $type = ConsultType::findOrFail($validated['consult_type_id']);

        if ($type->is_free) {
            return response()->json(['message' => 'This consult type does not require payment.'], 422);
        }

        if (!$type->price || $type->price <= 0) {
            return response()->json(['message' => 'Price not configured for this consult type. Contact support.'], 422);
        }

        $date = Carbon::parse($validated['preferred_date']);

        $existing = Booking::whereDate('preferred_date', $date->toDateString())
            ->where('preferred_time', $validated['preferred_time'])
            ->whereIn('status', ['pending', 'confirmed', 'awaiting_payment'])
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'That time slot was just taken. Please pick another.'], 422);
        }

        $booking = Booking::create([
            ...$validated,
            'status' => 'awaiting_payment',
            'public_booking_token' => Str::random(64),
        ]);

        // Build Stripe line items — consult type + any selected add-ons
        $addonCatalog = [
            'seo_audit' => ['name' => 'SEO Audit', 'price' => 150],
            'competitor_analysis' => ['name' => 'Competitor Analysis', 'price' => 100],
            'thirty_day_plan' => ['name' => '30-Day SEO Plan', 'price' => 250],
            'strategy_followup' => ['name' => 'Strategy Follow-up', 'price' => 75],
        ];

        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => (int) ($type->price * 100),
                    'product_data' => [
                        'name' => $type->name,
                        'description' => $type->formattedDuration() . ' consultation — seoaico.com',
                    ],
                ],
                'quantity' => 1,
            ],
        ];

        $selectedAddOns = $validated['add_ons'] ?? [];
        foreach ($selectedAddOns as $slug) {
            if (isset($addonCatalog[$slug])) {
                $addon = $addonCatalog[$slug];
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $addon['price'] * 100,
                        'product_data' => ['name' => $addon['name']],
                    ],
                    'quantity' => 1,
                ];
            }
        }

        try {
            $session = Cashier::stripe()->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $booking->email,
                'line_items' => $lineItems,
                'metadata' => ['booking_id' => $booking->id],
                'success_url' => url('/book/payment-return/' . $booking->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url('/book?payment=cancelled'),
                'expires_at' => now()->addMinutes(30)->timestamp,
            ]);
        } catch (\Exception $e) {
            $booking->delete();
            Log::channel('booking')->error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Payment initialisation failed. Please try again.'], 500);
        }

        $booking->update(['stripe_checkout_session_id' => $session->id]);

        return response()->json(['checkout_url' => $session->url]);
    }

    /**
     * Handle Stripe's return redirect after a paid booking checkout.
     * Verifies payment, creates the calendar event, and sends confirmation emails.
     */
    public function handlePaymentReturn(Booking $booking, Request $request): RedirectResponse
    {
        $sessionId = (string) $request->query('session_id', '');

        if (!$sessionId || $booking->stripe_checkout_session_id !== $sessionId) {
            abort(403, 'Invalid payment return.');
        }

        // Already confirmed — safe to redirect to upgrade (handles double-hits / webhook races)
        if ($booking->status === 'confirmed') {
            return redirect()->route('book.upgrade', ['booking' => $booking->id]);
        }

        try {
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                Log::channel('booking')->warning('Payment return with unpaid session', [
                    'booking_id' => $booking->id,
                    'payment_status' => $session->payment_status,
                ]);

                return redirect('/')->with('booking_error', 'Payment was not completed. Please try again.');
            }

            $booking->update(['stripe_payment_intent_id' => $session->payment_intent]);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Stripe session retrieval failed on payment return', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            // Proceed cautiously — charge likely succeeded, confirm the booking
        }

        // Attempt Google Calendar integration
        if (config('services.google.calendar_enabled', false)) {
            try {
                $calendarService = app(GoogleCalendarService::class);
                $result = $calendarService->createBookingEvent($booking);

                $booking->update([
                    'google_event_id' => $result['event_id'],
                    'google_meet_link' => $result['meet_link'],
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::channel('booking')->error('Google Calendar event creation failed for paid booking', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
                $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
            }
        } else {
            $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        }

        $booking->refresh()->load('consultType');

        // ── CRM: create or update lead — mark as paid ────────────────────────
        try {
            Lead::syncFromBooking($booking, 'paid', \App\Models\Lead::STAGE_PAID);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Lead sync failed for paid booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            Mail::to($booking->email)->queue(new BookingConfirmed($booking));
            Mail::to(config('services.booking.owner_email', 'hello@seoaico.com'))
                ->queue(new BookingAlert($booking));
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking email failed for paid booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Schedule pre-call primer 24 h before the session
        try {
            $sessionDateTime = Carbon::parse(
                $booking->preferred_date->toDateString() . ' ' . $booking->preferred_time
            );
            $preCallDelay = $sessionDateTime->copy()->subHours(24);
            if ($preCallDelay->isFuture()) {
                Mail::to($booking->email)
                    ->later($preCallDelay, new BookingPreCall($booking));
            }
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Pre-call email scheduling failed for paid booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('book.upgrade', ['booking' => $booking->id]);
    }

    /**
     * Booking confirmation page (legacy — linked from admin / emails).
     */
    public function confirm(Booking $booking)
    {
        $booking->load('consultType');

        return view('public.booking-confirm', compact('booking'));
    }

    /**
     * Show cancellation form.
     */
    public function cancel(Booking $booking)
    {
        $booking->load('consultType');

        return view('public.booking-cancel', compact('booking'));
    }

    /**
     * Process booking cancellation.
     */
    public function processCancel(Booking $booking): JsonResponse
    {
        if ($booking->isCancelled()) {
            return response()->json(['message' => 'Booking already cancelled.'], 400);
        }

        if ($booking->google_event_id) {
            try {
                $calendarService = app(GoogleCalendarService::class);
                $calendarService->cancelEvent($booking->google_event_id);
            } catch (\Exception $e) {
                Log::channel('booking')->error('Failed to cancel Google event', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Booking cancelled.']);
    }

    /**
     * Fallback when Google Calendar is unavailable — use DB bookings only.
     */
    private function getFallbackSlots(Carbon $date, int $durationMinutes): array
    {
        $availability = BookingAvailability::active()
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (!$availability) {
            return [];
        }

        $start = $date->copy()->setTimeFromTimeString($availability->start_time);
        $end = $date->copy()->setTimeFromTimeString($availability->end_time);

        $booked = Booking::whereDate('preferred_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed', 'awaiting_payment'])
            ->pluck('preferred_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $slots = [];
        $cursor = $start->copy();

        while ($cursor->copy()->addMinutes($durationMinutes)->lte($end)) {
            $time = $cursor->format('H:i');
            if (!in_array($time, $booked)) {
                $slots[] = $time;
            }
            $cursor->addMinutes(30);
        }

        return $slots;
    }
}
