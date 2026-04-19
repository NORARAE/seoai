<?php

namespace Tests\Feature\Booking;

use App\Mail\BookingAlert;
use App\Mail\BookingConfirmed;
use App\Models\Booking;
use App\Models\BookingAvailability;
use App\Models\ConsultType;
use App\Models\Lead;
use App\Models\QuickScan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private ConsultType $freeType;
    private ConsultType $consultationType;
    private ConsultType $activationType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->freeType = ConsultType::create([
            'name' => 'Free Discovery Call',
            'slug' => 'free-discovery-call',
            'description' => 'A free 15-minute discovery call.',
            'duration_minutes' => 15,
            'price' => 0,
            'is_free' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->consultationType = ConsultType::create([
            'name' => 'AI Visibility Consultation',
            'slug' => 'ai-visibility-consultation',
            'description' => 'A paid consultation for qualification and strategic direction.',
            'duration_minutes' => 60,
            'price' => 250,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->activationType = ConsultType::create([
            'name' => 'Full System Activation',
            'slug' => 'full-system-activation',
            'description' => 'A paid activation engagement for full system deployment.',
            'duration_minutes' => 60,
            'price' => 5000,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        foreach ([1, 2, 3, 4, 5] as $dow) {
            BookingAvailability::create([
                'day_of_week' => $dow,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'is_active' => true,
            ]);
        }

        Mail::fake();
    }

    // -------------------------------------------------------------------------
    // /book page
    // -------------------------------------------------------------------------

    public function test_book_page_loads_with_consult_types(): void
    {
        $response = $this->get('/book');
        $response->assertOk();
        $response->assertSee('AI Visibility Consultation');
        $response->assertSee('Full System Activation');
    }

    public function test_book_page_resolves_high_ticket_buttons_with_legacy_slugs(): void
    {
        // Keep only legacy slugs to verify fallback mapping used by the modal CTA buttons.
        ConsultType::query()->delete();

        $consultation = ConsultType::create([
            'name' => 'Legacy Consultation',
            'slug' => 'strategy-session',
            'description' => 'Legacy consultation option',
            'duration_minutes' => 90,
            'price' => 500,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $activation = ConsultType::create([
            'name' => 'Legacy Expansion Activation',
            'slug' => 'market-expansion',
            'description' => 'Legacy activation option',
            'duration_minutes' => 60,
            'price' => 5000,
            'is_free' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->get('/book');

        $response->assertOk();
        $response->assertSee("_bkOpenHT({$consultation->id}, 90, 'AI Visibility Consultation', 'full_prepay')", false);
        $response->assertSee("_bkOpenHT({$activation->id}, 60, 'Full System Activation', '50_50_split')", false);
    }

    // -------------------------------------------------------------------------
    // GET /book/slots
    // -------------------------------------------------------------------------

    public function test_slots_endpoint_returns_available_times(): void
    {
        $monday = $this->nextWeekday(Carbon::MONDAY);

        $response = $this->getJson('/book/slots?' . http_build_query([
            'date' => $monday,
            'consult_type_id' => $this->freeType->id,
        ]));

        $response->assertOk();
        $response->assertJsonStructure(['slots']);
        $this->assertNotEmpty($response->json('slots'));
        $this->assertContains('09:00', $response->json('slots'));
    }

    public function test_slots_returns_empty_for_weekend(): void
    {
        $saturday = $this->nextWeekday(Carbon::SATURDAY);

        $response = $this->getJson('/book/slots?' . http_build_query([
            'date' => $saturday,
            'consult_type_id' => $this->freeType->id,
        ]));

        $response->assertOk();
        $this->assertEmpty($response->json('slots'));
    }

    public function test_slots_validates_required_fields(): void
    {
        $this->getJson('/book/slots')->assertUnprocessable();
    }

    // -------------------------------------------------------------------------
    // POST /book (free booking)
    // -------------------------------------------------------------------------

    public function test_free_booking_stores_record_and_returns_success(): void
    {
        $date = $this->nextWeekday(Carbon::MONDAY);
        $this->createCompletedScan('alice@example.com');

        $response = $this->postJson('/book', [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Alice Tester',
            'email' => 'alice@example.com',
            'phone' => '555-0100',
            'company' => 'Acme Corp',
            'website' => 'https://acme.com',
            'message' => 'Looking forward to it.',
            'preferred_date' => $date,
            'preferred_time' => '10:00',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('bookings', [
            'email' => 'alice@example.com',
            'preferred_time' => '10:00',
            'status' => 'pending',
        ]);
    }

    public function test_free_booking_syncs_lead_record(): void
    {
        $date = $this->nextWeekday(Carbon::MONDAY);
        $this->createCompletedScan('bob@example.com');

        $this->postJson('/book', [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Bob Lead',
            'email' => 'bob@example.com',
            'preferred_date' => $date,
            'preferred_time' => '11:00',
        ])->assertOk();

        $this->assertDatabaseHas('leads', [
            'email' => 'bob@example.com',
            'source' => 'booking',
            'lifecycle_stage' => Lead::STAGE_BOOKED,
        ]);
    }

    public function test_free_booking_queues_confirmation_and_alert_emails(): void
    {
        $date = $this->nextWeekday(Carbon::TUESDAY);
        $this->createCompletedScan('carol@example.com');

        $this->postJson('/book', [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Carol Q',
            'email' => 'carol@example.com',
            'preferred_date' => $date,
            'preferred_time' => '14:00',
        ])->assertOk();

        Mail::assertQueued(BookingConfirmed::class, fn($m) => $m->hasTo('carol@example.com'));
        Mail::assertQueued(BookingAlert::class);
    }

    // -------------------------------------------------------------------------
    // Duplicate slot guard
    // -------------------------------------------------------------------------

    public function test_duplicate_slot_is_blocked(): void
    {
        $date = $this->nextWeekday(Carbon::WEDNESDAY);
        $this->createCompletedScan('dave@example.com');
        $this->createCompletedScan('eve@example.com');

        $payload = [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Dave First',
            'email' => 'dave@example.com',
            'preferred_date' => $date,
            'preferred_time' => '09:00',
        ];

        $this->postJson('/book', $payload)->assertOk();

        $this->postJson('/book', array_merge($payload, [
            'name' => 'Eve Second',
            'email' => 'eve@example.com',
        ]))->assertUnprocessable()
            ->assertJsonFragment(['message' => 'That time slot was just taken. Please pick another.']);
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

    public function test_booking_requires_name_and_email(): void
    {
        $this->postJson('/book', [
            'consult_type_id' => $this->freeType->id,
            'preferred_date' => $this->nextWeekday(Carbon::MONDAY),
            'preferred_time' => '10:00',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_booking_rejects_past_date(): void
    {
        $this->postJson('/book', [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Frank Past',
            'email' => 'frank@example.com',
            'preferred_date' => '2020-01-01',
            'preferred_time' => '10:00',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['preferred_date']);
    }

    public function test_booking_rejects_invalid_time_format(): void
    {
        $this->postJson('/book', [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Grace Bad',
            'email' => 'grace@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::MONDAY),
            'preferred_time' => '10:65',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['preferred_time']);
    }

    public function test_booking_rejects_invalid_website_url(): void
    {
        $this->postJson('/book', [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Hank Bad',
            'email' => 'hank@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::MONDAY),
            'preferred_time' => '10:00',
            'website' => 'not-a-url',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['website']);
    }

    // -------------------------------------------------------------------------
    // GET /book/confirmed
    // -------------------------------------------------------------------------

    public function test_confirmed_page_renders_for_pending_booking(): void
    {
        $booking = Booking::create([
            'consult_type_id' => $this->freeType->id,
            'name' => 'Irene Conf',
            'email' => 'irene@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::THURSDAY),
            'preferred_time' => '13:00',
            'status' => 'pending',
        ]);

        $this->get('/book/confirmed?booking=' . $booking->id)
            ->assertOk()
            ->assertSee('Free Discovery Call');
    }

    public function test_confirmed_page_404s_for_cancelled_booking(): void
    {
        $booking = Booking::create([
            'consult_type_id' => $this->freeType->id,
            'name' => 'Jack Canc',
            'email' => 'jack@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::FRIDAY),
            'preferred_time' => '15:00',
            'status' => 'cancelled',
        ]);

        $this->get('/book/confirmed?booking=' . $booking->id)->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // Cancellation flow
    // -------------------------------------------------------------------------

    public function test_cancel_page_renders_for_booking(): void
    {
        $booking = Booking::create([
            'consult_type_id' => $this->freeType->id,
            'name' => 'Karen Cancel',
            'email' => 'karen@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::MONDAY),
            'preferred_time' => '09:30',
            'status' => 'confirmed',
        ]);

        $this->get('/book/cancel/' . $booking->id)->assertOk();
    }

    public function test_process_cancel_marks_booking_cancelled(): void
    {
        $booking = Booking::create([
            'consult_type_id' => $this->freeType->id,
            'name' => 'Leo Cancel',
            'email' => 'leo@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::TUESDAY),
            'preferred_time' => '10:30',
            'status' => 'confirmed',
        ]);

        $this->postJson('/book/cancel/' . $booking->id)
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
    }

    public function test_process_cancel_is_idempotent_for_already_cancelled_booking(): void
    {
        $booking = Booking::create([
            'consult_type_id' => $this->freeType->id,
            'name' => 'Mia Already',
            'email' => 'mia@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::WEDNESDAY),
            'preferred_time' => '11:30',
            'status' => 'cancelled',
        ]);

        $this->postJson('/book/cancel/' . $booking->id)->assertStatus(400);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function nextWeekday(int $dayOfWeek): string
    {
        $date = Carbon::now()->addDay();
        while ($date->dayOfWeek !== $dayOfWeek) {
            $date->addDay();
        }

        return $date->toDateString();
    }

    private function createCompletedScan(string $email): QuickScan
    {
        return QuickScan::create([
            'email' => $email,
            'url' => 'https://example.com',
            'status' => QuickScan::STATUS_SCANNED,
            'paid' => true,
            'score' => 72,
            'scanned_at' => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Paid booking — /book/checkout
    // -------------------------------------------------------------------------

    public function test_checkout_rejects_when_stripe_not_configured(): void
    {
        // With no Stripe secret set in test env, Cashier::stripe() will throw.
        // The controller catches the exception and returns 500.
        // This test verifies the route exists and the payload is validated.
        $date = $this->nextWeekday(Carbon::MONDAY);

        // Validation still runs before Stripe is touched, so a missing field = 422
        $this->postJson('/book/checkout', [
            'consult_type_id' => $this->consultationType->id,
            // missing required fields
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'preferred_date', 'preferred_time']);
    }

    public function test_checkout_rejects_free_type(): void
    {
        $date = $this->nextWeekday(Carbon::MONDAY);

        $this->postJson('/book/checkout', [
            'consult_type_id' => $this->freeType->id,
            'name' => 'Alice Free',
            'email' => 'alice@example.com',
            'preferred_date' => $date,
            'preferred_time' => '10:00',
        ])->assertUnprocessable()
            ->assertJsonFragment(['message' => 'This consult type does not require payment.']);
    }

    public function test_checkout_duplicate_slot_guard_includes_awaiting_payment(): void
    {
        $date = $this->nextWeekday(Carbon::MONDAY);

        // Seed an awaiting_payment booking for the same slot
        Booking::create([
            'consult_type_id' => $this->consultationType->id,
            'name' => 'Existing Payer',
            'email' => 'existing@example.com',
            'preferred_date' => $date,
            'preferred_time' => '10:00',
            'status' => 'awaiting_payment',
        ]);

        // A NEW checkout should be rejected because the slot is reserved
        $this->postJson('/book/checkout', [
            'consult_type_id' => $this->consultationType->id,
            'name' => 'New Person',
            'email' => 'new@example.com',
            'preferred_date' => $date,
            'preferred_time' => '10:00',
        ])->assertUnprocessable()
            ->assertJsonFragment(['message' => 'That time slot was just taken. Please pick another.']);
    }

    public function test_awaiting_payment_slot_excluded_from_available_slots(): void
    {
        $date = $this->nextWeekday(Carbon::MONDAY);

        // Seed an awaiting_payment booking
        Booking::create([
            'consult_type_id' => $this->consultationType->id,
            'name' => 'Paying Person',
            'email' => 'pay@example.com',
            'preferred_date' => $date,
            'preferred_time' => '09:00',
            'status' => 'awaiting_payment',
        ]);

        $response = $this->getJson('/book/slots?' . http_build_query([
            'date' => $date,
            'consult_type_id' => $this->consultationType->id,
        ]));

        $response->assertOk();
        $this->assertNotContains('09:00', $response->json('slots'));
    }

    public function test_confirmed_page_renders_for_awaiting_payment_booking(): void
    {
        $booking = Booking::create([
            'consult_type_id' => $this->consultationType->id,
            'name' => 'Pay Wait',
            'email' => 'paywait@example.com',
            'preferred_date' => $this->nextWeekday(Carbon::THURSDAY),
            'preferred_time' => '14:00',
            'status' => 'awaiting_payment',
        ]);

        $this->get('/book/confirmed?booking=' . $booking->id)
            ->assertOk()
            ->assertSee('AI Visibility Consultation');
    }
}
