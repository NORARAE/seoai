<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Lead;
use App\Services\GoogleCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * Handles Stripe webhooks for the booking payment flow.
 *
 * Endpoint: POST /api/v1/book/stripe-webhook
 *
 * This controller's sole job is to confirm bookings that reach
 * `checkout.session.completed` (mode=payment) — covering the case where
 * the customer pays but closes the browser before being redirected back to
 * /book/payment-return/{booking}.  It is a 100% idempotent safety net.
 *
 * Security model (mirrors LicenseService::handleStripeWebhook):
 *   - Raw payload is read from the request body (NEVER from parsed JSON)
 *   - Stripe-Signature header is verified via Webhook::constructEvent()
 *   - Missing or invalid secret returns 400
 *   - Bad signature returns 400
 *   - All other errors return 500 with a booking-channel log entry
 *
 * Register this endpoint in your Stripe Dashboard as:
 *   https://seoaico.com/api/v1/book/stripe-webhook
 * Events to send: checkout.session.completed, invoice.payment_failed
 */
class BookingWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = (string) config('services.stripe_booking.webhook_secret', '');

        // ── Signature verification ────────────────────────────────────────────
        if ($secret === '') {
            Log::channel('booking')->error('Booking Stripe webhook secret is not configured.');

            return response()->json(['message' => 'Webhook secret not configured.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $event = Webhook::constructEvent($payload, (string) $sigHeader, $secret);
        } catch (UnexpectedValueException $e) {
            Log::channel('booking')->warning('Booking webhook: malformed payload', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid payload.'], Response::HTTP_BAD_REQUEST);
        } catch (SignatureVerificationException $e) {
            Log::channel('booking')->warning('Booking webhook: invalid signature', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid signature.'], Response::HTTP_BAD_REQUEST);
        }

        // ── Route by event type ────────────────────────────────────────────────
        if ($event->type === 'invoice.payment_failed') {
            return $this->handleInvoicePaymentFailed($event->data->object);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response()->json(['message' => 'Event type not handled.', 'event' => $event->type]);
        }

        $session = $event->data->object;

        // Handle both one-time payment and subscription checkout completions.
        if (!in_array($session->mode ?? null, ['payment', 'subscription'])) {
            return response()->json(['message' => 'Unsupported checkout mode.']);
        }

        if (($session->payment_status ?? null) !== 'paid') {
            // Not yet paid (e.g., async payment method still pending) — Stripe
            // will send a `checkout.session.async_payment_succeeded` later.
            return response()->json(['message' => 'Payment not yet completed.']);
        }

        $bookingId = (int) ($session->metadata?->booking_id ?? 0);

        if (!$bookingId) {
            Log::channel('booking')->warning('Booking webhook: missing booking_id in metadata', [
                'session_id' => $session->id,
            ]);

            return response()->json(['message' => 'No booking_id in metadata.']);
        }

        // ── Find and confirm the booking ──────────────────────────────────────
        $booking = Booking::with('consultType')->find($bookingId);

        if (!$booking) {
            Log::channel('booking')->error('Booking webhook: booking not found', [
                'booking_id' => $bookingId,
                'session_id' => $session->id,
            ]);

            // Return 200 so Stripe doesn't retry — the booking simply doesn't exist.
            return response()->json(['message' => 'Booking not found.']);
        }

        // Idempotent: already confirmed by the synchronous return handler.
        if ($booking->status === 'confirmed') {
            return response()->json(['message' => 'Already confirmed.', 'booking_id' => $bookingId]);
        }

        // Store payment reference if not already set (return handler may have beaten us).
        if (!$booking->stripe_payment_intent_id) {
            $reference = ($session->mode === 'subscription')
                ? ($session->subscription ?? null)
                : ($session->payment_intent ?? null);
            if ($reference) {
                $booking->stripe_payment_intent_id = (string) $reference;
            }
        }

        // ── Google Calendar event (if not already created) ────────────────────
        try {
            if (!$booking->google_event_id) {
                $calendarService = app(GoogleCalendarService::class);
                $result = $calendarService->createBookingEvent($booking);

                $booking->fill([
                    'google_event_id' => $result['event_id'],
                    'google_meet_link' => $result['meet_link'],
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking webhook: Calendar event creation failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
            // Calendar failure must not block confirmation.
        }

        $deploymentFields = [];
        if ($booking->isActivationEngagement() && !$booking->activation_date) {
            $deploymentFields = [
                'activation_date' => now(),
                'cycle_end_date' => now()->addMonths(4),
                'deployment_status' => 'active_deployment',
            ];
        }

        $booking->fill(array_merge(['status' => 'confirmed', 'confirmed_at' => now()], $deploymentFields));
        $booking->save();

        $booking->refresh()->loadMissing('consultType');

        // ── CRM: ensure lead is synced as paid ────────────────────────────────
        try {
            Lead::syncFromBooking($booking, 'paid', Lead::STAGE_PAID);
        } catch (\Exception $e) {
            Log::channel('booking')->error('Booking webhook: Lead sync failed', [
                'booking_id' => $bookingId,
                'error' => $e->getMessage(),
            ]);
        }

        Log::channel('booking')->info('Booking confirmed via Stripe webhook', [
            'booking_id' => $bookingId,
            'session_id' => $session->id,
        ]);

        return response()->json(['message' => 'Booking confirmed.', 'booking_id' => $bookingId]);
    }

    /**
     * Mark the booking's deployment as payment_failed when a subscription renewal fails.
     */
    private function handleInvoicePaymentFailed(object $invoice): JsonResponse
    {
        $subscriptionId = (string) ($invoice->subscription ?? '');

        if (!$subscriptionId) {
            return response()->json(['message' => 'No subscription ID on failed invoice.']);
        }

        $booking = Booking::where('stripe_payment_intent_id', $subscriptionId)->first();

        if (!$booking) {
            // Not a booking subscription — ignore.
            return response()->json(['message' => 'No booking found for subscription.']);
        }

        if ($booking->isActivationEngagement()) {
            $booking->update(['deployment_status' => 'payment_failed']);
        }

        Log::channel('booking')->warning('Booking subscription payment failed', [
            'booking_id' => $booking->id,
            'subscription_id' => $subscriptionId,
        ]);

        return response()->json(['message' => 'Booking deployment status updated to payment_failed.']);
    }
}
