<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\ConsultType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingWebhookFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_consultation_webhook_confirms_paid_booking_without_activation_fields(): void
    {
        config()->set('services.stripe_booking.webhook_secret', 'whsec_booking_test');

        $consultationType = ConsultType::create([
            'name' => 'AI Visibility Consultation',
            'slug' => 'ai-visibility-consultation',
            'description' => 'Qualification and strategic direction',
            'duration_minutes' => 60,
            'price' => 500,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $booking = Booking::create([
            'consult_type_id' => $consultationType->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time' => '10:00',
            'status' => 'awaiting_payment',
            'stripe_checkout_session_id' => 'cs_consultation_123',
        ]);

        $event = [
            'id' => 'evt_consultation_paid',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_consultation_123',
                    'mode' => 'payment',
                    'payment_status' => 'paid',
                    'payment_intent' => 'pi_consultation_123',
                    'metadata' => [
                        'booking_id' => (string) $booking->id,
                    ],
                ],
            ],
        ];

        $payload = json_encode($event, JSON_THROW_ON_ERROR);
        $signature = $this->stripeSignature($payload, 'whsec_booking_test');

        $response = $this->call(
            'POST',
            '/api/v1/book/stripe-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk()->assertJsonFragment(['message' => 'Booking confirmed.']);

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('pi_consultation_123', $booking->stripe_payment_intent_id);
        $this->assertNull($booking->activation_date);
        $this->assertNull($booking->deployment_status);
    }

    public function test_activation_webhook_confirms_paid_booking_with_activation_fields(): void
    {
        config()->set('services.stripe_booking.webhook_secret', 'whsec_booking_test');

        $activationType = ConsultType::create([
            'name' => 'Full System Activation',
            'slug' => 'full-system-activation',
            'description' => 'Full build and deployment',
            'duration_minutes' => 60,
            'price' => 5000,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $booking = Booking::create([
            'consult_type_id' => $activationType->id,
            'name' => 'Activation User',
            'email' => 'activation@example.com',
            'preferred_date' => now()->addDays(3)->toDateString(),
            'preferred_time' => '11:00',
            'status' => 'awaiting_payment',
            'stripe_checkout_session_id' => 'cs_activation_123',
        ]);

        $event = [
            'id' => 'evt_activation_paid',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_activation_123',
                    'mode' => 'payment',
                    'payment_status' => 'paid',
                    'payment_intent' => 'pi_activation_123',
                    'metadata' => [
                        'booking_id' => (string) $booking->id,
                    ],
                ],
            ],
        ];

        $payload = json_encode($event, JSON_THROW_ON_ERROR);
        $signature = $this->stripeSignature($payload, 'whsec_booking_test');

        $response = $this->call(
            'POST',
            '/api/v1/book/stripe-webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk()->assertJsonFragment(['message' => 'Booking confirmed.']);

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('pi_activation_123', $booking->stripe_payment_intent_id);
        $this->assertNotNull($booking->activation_date);
        $this->assertSame('active_deployment', $booking->deployment_status);
    }

    private function stripeSignature(string $payload, string $secret): string
    {
        $timestamp = time();
        $signedPayload = $timestamp . '.' . $payload;
        $hash = hash_hmac('sha256', $signedPayload, $secret);

        return "t={$timestamp},v1={$hash}";
    }
}
