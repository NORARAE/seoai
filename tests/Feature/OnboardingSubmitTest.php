<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\OnboardingSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression tests for POST /onboarding/submit.
 *
 * Guards against re-introduction of "Undefined array key" errors for optional
 * fields (phone, booking_id) that are absent from the standard onboarding form.
 */
class OnboardingSubmitTest extends TestCase
{
    use RefreshDatabase;

    /** Minimum required payload (preview mode — no booking_id, no phone). */
    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'business_name' => 'Test Business LLC',
            'primary_contact' => 'Jane Doe',
            'email' => 'jane@example.com',
            // phone intentionally omitted — this is the regression guard
            // booking_id intentionally omitted — preview mode
        ], $overrides);
    }

    /** @test */
    public function submit_without_phone_does_not_500(): void
    {
        $response = $this->post('/onboarding/submit', $this->basePayload());

        // Anything except 500 is acceptable: 302 redirect → done, or 422 validation
        $this->assertNotEquals(
            500,
            $response->getStatusCode(),
            'POST /onboarding/submit must not return 500 when phone is absent'
        );
    }

    /** @test */
    public function submit_without_booking_id_does_not_500(): void
    {
        $response = $this->post('/onboarding/submit', $this->basePayload());

        $this->assertNotEquals(
            500,
            $response->getStatusCode(),
            'POST /onboarding/submit must not return 500 when booking_id is absent'
        );
    }

    /** @test */
    public function submit_with_honeypot_filled_redirects_silently(): void
    {
        $response = $this->post('/onboarding/submit', array_merge(
            $this->basePayload(),
            ['website_confirm' => 'bot-value']
        ));

        // Honeypot path redirects to done without processing
        $response->assertRedirect(route('onboarding.done'));
    }

    /** @test */
    public function successful_preview_submission_creates_records(): void
    {
        $response = $this->post('/onboarding/submit', $this->basePayload());

        // Must redirect — not 500
        $response->assertRedirect();

        $lead = Lead::where('email', 'jane@example.com')->first();
        $this->assertNotNull($lead, 'Lead should be created for preview submission');
        $this->assertNull($lead->phone, 'Phone should be stored as null when omitted');

        $this->assertDatabaseHas('onboarding_submissions', [
            'lead_id' => $lead->id,
            'business_name' => 'Test Business LLC',
            'phone' => null,
        ]);
    }
}
