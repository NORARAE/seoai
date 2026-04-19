<?php

namespace App\Http\Controllers;

use App\Mail\BookingAlert;
use App\Mail\BookingConfirmed;
use App\Mail\BookingFollowUp;
use App\Mail\BookingPreCall;
use App\Mail\AuditWhatToPrepare;
use App\Models\Booking;
use App\Models\BookingAvailability;
use App\Models\ConsultType;
use App\Models\EmailLog;
use App\Models\FunnelEvent;
use App\Models\Lead;
use App\Models\QuickScan;
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
        $highTicketTypes = $this->resolveEntryTypes($types);

        FunnelEvent::fire(FunnelEvent::BOOKING_VIEWED);

        return view('public.book', compact('types', 'availableDays', 'highTicketTypes'));
    }

    /**
     * Return available time slots for a date + consult type (AJAX).
     */
    public function getSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after:today',
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

        // Strip slots within the 24-hour advance booking window
        $cutoff = now()->addHours(24);

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

        // Filter out slots inside the 24-hour advance window
        $slots = array_values(array_filter($slots, function (string $time) use ($date, $cutoff) {
            return Carbon::parse($date->toDateString() . ' ' . $time)->greaterThan($cutoff);
        }));

        return response()->json(['slots' => $slots]);
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request): JsonResponse
    {
        // Honeypot — bots fill hidden fields; legitimate users never do
        if ($request->filled('website_confirm')) {
            return response()->json(['message' => 'Invalid request.'], 422);
        }

        // Normalize website — auto-prefix https:// if no scheme provided and value looks like a domain
        if ($request->filled('website') && !preg_match('#^https?://#i', $request->website) && str_contains($request->website, '.')) {
            $request->merge(['website' => 'https://' . $request->website]);
        }

        $validated = $request->validate([
            'consult_type_id' => 'required|exists:consult_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:500',
            'message' => 'nullable|string|max:2000',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|date_format:H:i',
        ]);

        $type = ConsultType::findOrFail($validated['consult_type_id']);

        // ── Free booking access control ──────────────────────────────────────
        // Free sessions require a completed scan and are limited to one per email.
        if ($type->is_free) {
            $hasScan = QuickScan::where('email', $validated['email'])
                ->where('status', QuickScan::STATUS_SCANNED)
                ->exists();

            if (!$hasScan) {
                return response()->json([
                    'message' => 'A completed scan is required before booking a free session. Start with a citation scan first.',
                ], 422);
            }

            $existingFreeBooking = Booking::where('email', $validated['email'])
                ->whereHas('consultType', fn($q) => $q->where('is_free', true))
                ->whereNotIn('status', ['cancelled'])
                ->exists();

            if ($existingFreeBooking) {
                return response()->json([
                    'message' => 'You already have a free session booked. To continue exploring, consider our paid advisory or activation options.',
                ], 422);
            }
        }

        $date = Carbon::parse($validated['preferred_date']);

        // 24-hour advance booking rule
        $bookingDateTime = Carbon::parse($date->toDateString() . ' ' . $validated['preferred_time']);
        if ($bookingDateTime->lessThanOrEqualTo(now()->addHours(24))) {
            return response()->json([
                'message' => 'This session must be scheduled at least 24 hours in advance.',
            ], 422);
        }

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
            'booking_type' => $this->resolveBookingType($type->slug),
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
            $lead = Lead::syncFromBooking(
                $booking,
                $booking->consultType->is_free ? 'free' : null,
                \App\Models\Lead::STAGE_BOOKED
            );
            // Pipeline tag: Booked Call
            $existing_tags = $lead->tags ?? [];
            if (!in_array('lead:booked', $existing_tags)) {
                $lead->update(['tags' => array_merge($existing_tags, ['lead:booked'])]);
            }
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
            EmailLog::create([
                'booking_id' => $booking->id,
                'email_type' => 'confirmation',
                'recipient_email' => $booking->email,
                'sent_at' => now(),
                'status' => 'sent',
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking email failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            EmailLog::create([
                'booking_id' => $booking->id,
                'email_type' => 'confirmation',
                'recipient_email' => $booking->email,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
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
                EmailLog::create([
                    'booking_id' => $booking->id,
                    'email_type' => 'pre_call',
                    'recipient_email' => $booking->email,
                    'sent_at' => $preCallDelay,
                    'status' => 'scheduled',
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Pre-call email scheduling failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Schedule post-session follow-up 24 h after the session
        try {
            $sessionDateTime = Carbon::parse(
                $booking->preferred_date->toDateString() . ' ' . $booking->preferred_time
            );
            $followUpDelay = $sessionDateTime->copy()->addHours(24);
            Mail::to($booking->email)
                ->later($followUpDelay, new BookingFollowUp($booking));
            EmailLog::create([
                'booking_id' => $booking->id,
                'email_type' => 'follow_up',
                'recipient_email' => $booking->email,
                'sent_at' => $followUpDelay,
                'status' => 'scheduled',
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Follow-up email scheduling failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Funnel tracking
        FunnelEvent::fire(FunnelEvent::BOOKING_CREATED, $booking->id);

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
        // Honeypot
        if ($request->filled('website_confirm')) {
            return response()->json(['message' => 'Invalid request.'], 422);
        }

        // Normalize website — auto-prefix https:// if no scheme provided and value looks like a domain
        if ($request->filled('website') && !preg_match('#^https?://#i', $request->website) && str_contains($request->website, '.')) {
            $request->merge(['website' => 'https://' . $request->website]);
        }

        $validated = $request->validate([
            'consult_type_id' => 'required|exists:consult_types,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:500',
            'message' => 'nullable|string|max:2000',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required|date_format:H:i',
            'add_ons' => 'nullable|array|max:4',
            'add_ons.*' => 'string|in:seo_audit,competitor_analysis,thirty_day_plan,strategy_followup',
            'payment_structure' => 'nullable|in:full_prepay,50_50_split,activation_plus_subscription',
            'recommended_tier' => 'nullable|in:core,multi_market_standard,multi_market_custom,agency_partner',
        ]);

        $type = ConsultType::findOrFail($validated['consult_type_id']);

        if ($type->is_free) {
            return response()->json(['message' => 'This consult type does not require payment.'], 422);
        }

        if (!$type->price || $type->price <= 0) {
            return response()->json(['message' => 'Price not configured for this consult type. Contact support.'], 422);
        }

        $date = Carbon::parse($validated['preferred_date']);

        // 24-hour advance booking rule
        $bookingDateTime = Carbon::parse($date->toDateString() . ' ' . $validated['preferred_time']);
        if ($bookingDateTime->lessThanOrEqualTo(now()->addHours(24))) {
            return response()->json([
                'message' => 'This session must be scheduled at least 24 hours in advance.',
            ], 422);
        }

        $existing = Booking::whereDate('preferred_date', $date->toDateString())
            ->where('preferred_time', $validated['preferred_time'])
            ->whereIn('status', ['pending', 'confirmed', 'awaiting_payment'])
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'That time slot was just taken. Please pick another.'], 422);
        }

        $booking = Booking::create([
            ...$validated,
            'booking_type' => $this->resolveBookingType($type->slug),
            'status' => 'awaiting_payment',
            'public_booking_token' => Str::random(64),
        ]);

        // Build Stripe line items — consult type + any selected add-ons
        FunnelEvent::fire(FunnelEvent::BOOKING_CREATED, $booking->id);

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
            $isSubscription = ($validated['payment_structure'] ?? null) === 'activation_plus_subscription';

            if ($isSubscription) {
                // Resolve Stripe tier config key from recommended_tier
                $tierConfigKey = match ($validated['recommended_tier'] ?? 'core') {
                    'multi_market_standard', 'multi_market_custom' => 'multi',
                    'agency_partner' => 'agency',
                    default => 'core',
                };

                $monthlyPriceId = config('services.stripe_tiers.' . $tierConfigKey . '.monthly');
                $activationPriceId = config('services.stripe_tiers.' . $tierConfigKey . '.activation');

                // Build subscription line items — prefer pre-configured price IDs,
                // fall back to inline price_data when IDs are not yet provisioned.
                if ($monthlyPriceId) {
                    $subscriptionLineItems = [['price' => $monthlyPriceId, 'quantity' => 1]];
                } else {
                    $subscriptionLineItems = [
                        [
                            'price_data' => [
                                'currency' => 'usd',
                                'unit_amount' => (int) ($type->price * 100),
                                'recurring' => ['interval' => 'month'],
                                'product_data' => [
                                    'name' => config('services.stripe_tiers.' . $tierConfigKey . '.product_name', $type->name),
                                    'description' => 'SEO AI Co™ — structured 4-month deployment cycle',
                                ],
                            ],
                            'quantity' => 1,
                        ],
                    ];
                }

                // Activation fee — charged once on first invoice
                $addInvoiceItems = [];
                if ($activationPriceId) {
                    $addInvoiceItems[] = ['price' => $activationPriceId, 'quantity' => 1];
                }

                $sessionParams = [
                    'mode' => 'subscription',
                    'customer_email' => $booking->email,
                    'line_items' => $subscriptionLineItems,
                    'metadata' => [
                        'booking_id' => $booking->id,
                        'payment_structure' => 'activation_plus_subscription',
                        'tier' => $tierConfigKey,
                        'service' => $type->slug,
                        'source' => 'landing',
                        'timestamp' => now()->toISOString(),
                    ],
                    'subscription_data' => [
                        'metadata' => ['booking_id' => $booking->id],
                    ],
                    'success_url' => url('/book/payment-return/' . $booking->id) . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => url('/book?payment=cancelled'),
                    'expires_at' => now()->addMinutes(30)->timestamp,
                ];

                if (!empty($addInvoiceItems)) {
                    $sessionParams['add_invoice_items'] = $addInvoiceItems;
                }

                $session = Cashier::stripe()->checkout->sessions->create($sessionParams);
            } else {
                $session = Cashier::stripe()->checkout->sessions->create([
                    'mode' => 'payment',
                    'customer_email' => $booking->email,
                    'line_items' => $lineItems,
                    'metadata' => [
                        'booking_id' => $booking->id,
                        'service' => $type->slug,
                        'tier' => (string) (int) $type->price,
                        'source' => 'landing',
                        'timestamp' => now()->toISOString(),
                    ],
                    'success_url' => url('/book/payment-return/' . $booking->id) . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => url('/book?payment=cancelled'),
                    'expires_at' => now()->addMinutes(30)->timestamp,
                ]);
            }
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

            // Store reference: payment_intent for one-time, subscription ID for recurring
            $reference = ($session->mode === 'subscription')
                ? ($session->subscription ?? null)
                : ($session->payment_intent ?? null);

            $booking->update(array_merge([
                'stripe_payment_intent_id' => $reference,
                'payment_secured' => true,
            ], $this->deploymentFieldsFor($booking)));
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
            EmailLog::create([
                'booking_id' => $booking->id,
                'email_type' => 'confirmation',
                'recipient_email' => $booking->email,
                'sent_at' => now(),
                'status' => 'sent',
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking email failed for paid booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            EmailLog::create([
                'booking_id' => $booking->id,
                'email_type' => 'confirmation',
                'recipient_email' => $booking->email,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
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
                EmailLog::create([
                    'booking_id' => $booking->id,
                    'email_type' => 'pre_call',
                    'recipient_email' => $booking->email,
                    'sent_at' => $preCallDelay,
                    'status' => 'scheduled',
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Pre-call email scheduling failed for paid booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Schedule post-session follow-up 24 h after the session
        try {
            $sessionDateTime = Carbon::parse(
                $booking->preferred_date->toDateString() . ' ' . $booking->preferred_time
            );
            $followUpDelay = $sessionDateTime->copy()->addHours(24);
            Mail::to($booking->email)
                ->later($followUpDelay, new BookingFollowUp($booking));
            EmailLog::create([
                'booking_id' => $booking->id,
                'email_type' => 'follow_up',
                'recipient_email' => $booking->email,
                'sent_at' => $followUpDelay,
                'status' => 'scheduled',
            ]);
        } catch (\Exception $e) {
            Log::channel('booking')->warning('Follow-up email scheduling failed for paid booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }

        // ── Consultation prep email sequence ────────────────────────────────
        // "What to Prepare" email sent 48 h before consultation (falls back to 24 h)
        if ($booking->isConsultationEngagement()) {
            try {
                $sessionDateTime = Carbon::parse(
                    $booking->preferred_date->toDateString() . ' ' . $booking->preferred_time
                );
                $prepDelay = $sessionDateTime->copy()->subHours(48);
                if (!$prepDelay->isFuture()) {
                    $prepDelay = $sessionDateTime->copy()->subHours(24);
                }
                if ($prepDelay->isFuture()) {
                    Mail::to($booking->email)
                        ->later($prepDelay, new AuditWhatToPrepare($booking));
                    EmailLog::create([
                        'booking_id' => $booking->id,
                        'email_type' => 'audit_prep',
                        'recipient_email' => $booking->email,
                        'sent_at' => $prepDelay,
                        'status' => 'scheduled',
                    ]);
                }
            } catch (\Exception $e) {
                Log::channel('booking')->warning('Consultation prep email scheduling failed', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Funnel tracking
        FunnelEvent::fire(FunnelEvent::BOOKING_PAID, $booking->id);

        if ($booking->isActivationEngagement()) {
            return redirect()->route('onboarding.start', ['booking' => $booking->id]);
        }

        return redirect()->route('book.confirmed', ['booking' => $booking->id]);
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
     * Map a ConsultType slug to the booking_type enum value.
     */
    private function resolveBookingType(string $slug): string
    {
        return match ($slug) {
            'discovery' => 'discovery',
            'audit', 'consultation', 'ai-visibility-consultation',
            'strategy', 'agency-review', 'strategy-session',
            'seo-audit', 'project-scoping' => 'consultation',
            'activation', 'build', 'market-expansion', 'full-system-activation' => 'activation',
            default => 'discovery',
        };
    }

    private function resolveEntryTypes($types)
    {
        $paidTypes = $types->where('is_free', false)->values();

        $consultationType = $paidTypes->firstWhere('slug', 'ai-visibility-consultation')
            ?? $paidTypes->firstWhere('slug', 'consultation')
            ?? $paidTypes->firstWhere('slug', 'audit')
            ?? $paidTypes->firstWhere('slug', 'strategy-session')
            ?? $paidTypes->firstWhere('slug', 'strategy')
            ?? $paidTypes->sortBy('price')->first();

        $activationType = $paidTypes->firstWhere('slug', 'full-system-activation')
            ?? $paidTypes->firstWhere('slug', 'activation')
            ?? $paidTypes->firstWhere('slug', 'market-expansion')
            ?? $paidTypes->sortByDesc('price')->first();

        return collect([
            'consultation' => $consultationType,
            'activation' => $activationType,
        ]);
    }

    private function deploymentFieldsFor(Booking $booking): array
    {
        if (!$booking->isActivationEngagement()) {
            return [];
        }

        return [
            'activation_date' => now(),
            'cycle_end_date' => now()->addMonths(4),
            'deployment_status' => 'active_deployment',
        ];
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
