<?php

namespace Tests\Feature\Inquiry;

use App\Models\Inquiry;
use App\Models\SpamLog;
use App\Services\InquiryEnrichmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * InquiryFormTest
 *
 * Covers:
 *  - honeypot triggered (new field name: website_confirm)
 *  - time-to-submit too fast
 *  - duplicate email / IP suppression
 *  - high-risk silent rejection
 *  - normal valid submission pipeline
 *
 * The enrichment service is mocked so that tests run offline and deterministically.
 */
class InquiryFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // prevent inquiry duplicate-suppression keys leaking between tests
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'           => 'Test User',
            'company'        => 'Test Corp',
            'email'          => 'test@realcompany.com',
            'website'        => 'https://realcompany.com',
            'type'           => 'business',
            'tier'           => '5k',
            'niche'          => 'Law',
            'message'        => 'Looking for SEO solution for our business.',
            'form_loaded_at' => (string) (time() - 60),  // 60 seconds ago — valid
        ], $overrides);
    }

    private function mockEnrichment(array $override = []): InquiryEnrichmentService
    {
        $mock = $this->createMock(InquiryEnrichmentService::class);

        $defaults = [
            'ip_city'                => 'Seattle',
            'ip_region'              => 'Washington',
            'ip_country'             => 'US',
            'ip_isp'                 => 'Test ISP',
            'ip_is_proxy'            => false,
            'ip_is_hosting'          => false,
            'url_status'             => 'valid',
            'url_is_https'           => true,
            'domain_age_days'        => 400,
            'email_type'             => 'business',
            'company_enrichment'     => null,
            'time_to_submit_seconds' => 60,
            'recaptcha_score'        => null,
        ];

        $enrichData = array_merge($defaults, $override);

        $mock->method('enrichAll')->willReturn($enrichData);
        $mock->method('verifyRecaptcha')->willReturn(null);
        $mock->method('scoreSpamRisk')->willReturnCallback(
            // Delegate to the real scoring method for accuracy
            fn (array $data) => (new InquiryEnrichmentService())->scoreSpamRisk($data)
        );

        $this->app->instance(InquiryEnrichmentService::class, $mock);

        return $mock;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HONEYPOT
    // ──────────────────────────────────────────────────────────────────────────

    public function test_honeypot_website_confirm_triggers_silent_rejection(): void
    {
        Queue::fake();
        $this->mockEnrichment();

        $response = $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'website_confirm' => 'http://spam.example.com',  // bot filled honeypot
        ]));

        $response->assertRedirect();

        $inquiry = Inquiry::latest()->first();
        $this->assertNotNull($inquiry);
        $this->assertEquals('rejected', $inquiry->status);
        $this->assertTrue((bool) $inquiry->honeypot_triggered);

        // Admin email should NOT be queued
        Queue::assertNothingPushed();

        $spamLog = SpamLog::where('inquiry_id', $inquiry->id)->first();
        $this->assertNotNull($spamLog);
        $this->assertEquals('honeypot_triggered', $spamLog->reason);
    }

    public function test_legacy_website_url_honeypot_also_triggers_rejection(): void
    {
        Queue::fake();
        $this->mockEnrichment();

        $response = $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'website_url' => 'http://spam.example.com',  // legacy field name
        ]));

        $response->assertRedirect();
        $inquiry = Inquiry::latest()->first();
        $this->assertEquals('rejected', $inquiry->status);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // TIME TO SUBMIT
    // ──────────────────────────────────────────────────────────────────────────

    public function test_bot_speed_submission_raises_spam_risk(): void
    {
        Queue::fake();
        // form_loaded_at = 2 seconds ago (under 4 second threshold)
        $this->mockEnrichment(['time_to_submit_seconds' => 2]);

        $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'form_loaded_at' => (string) (time() - 2),
        ]));

        $inquiry = Inquiry::latest()->first();
        // 2s fast submit = +3 pts → medium or high depending on other signals
        $this->assertContains($inquiry->spam_risk, ['medium', 'high']);
        $this->assertEquals(2, $inquiry->time_to_submit_seconds);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DUPLICATE SUPPRESSION
    // ──────────────────────────────────────────────────────────────────────────

    public function test_duplicate_email_within_10_minutes_is_silently_rejected(): void
    {
        Queue::fake();
        $this->mockEnrichment();

        $testEmail = 'unique_test_dup@realcompany.com';
        $payload   = $this->validPayload(['email' => $testEmail]);

        // Simulate a prior submission by pre-seeding both cache keys
        // (replicating what the controller puts after a successful first submission)
        Cache::put('inquiry_email:' . md5(strtolower($testEmail)), 1, now()->addMinutes(10));
        Cache::put('inquiry_ip:' . md5('127.0.0.1'), 1, now()->addMinutes(10));

        // Submit — should be detected as duplicate and silently rejected
        $response = $this->post(route('licensing-inquiry.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('inquiry_success'); // still shows success (silent)

        $inquiry = Inquiry::where('email', $testEmail)->latest()->first();
        $this->assertNotNull($inquiry);
        $this->assertEquals('rejected', $inquiry->status);

        $spamLog = SpamLog::where('inquiry_id', $inquiry->id)->first();
        $this->assertNotNull($spamLog);
        $this->assertEquals('duplicate_submission', $spamLog->reason);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HIGH-RISK SILENT REJECTION
    // ──────────────────────────────────────────────────────────────────────────

    public function test_high_risk_submission_is_silently_rejected(): void
    {
        Queue::fake();
        // Proxy IP + disposable email = score 6 = high risk
        $this->mockEnrichment([
            'ip_is_proxy'   => true,
            'email_type'    => 'disposable',
        ]);

        $response = $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'email' => 'junk@mailinator.com',
        ]));

        $response->assertRedirect();
        // User sees normal success flash — not an error
        $response->assertSessionHas('inquiry_success');

        $inquiry = Inquiry::latest()->first();
        $this->assertEquals('high', $inquiry->spam_risk);
        $this->assertEquals('rejected', $inquiry->status);

        // No email queued to admin
        Queue::assertNothingPushed();

        $spamLog = SpamLog::where('inquiry_id', $inquiry->id)->first();
        $this->assertNotNull($spamLog);
        $this->assertEquals('high_risk_score', $spamLog->reason);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // VALID SUBMISSION
    // ──────────────────────────────────────────────────────────────────────────

    public function test_valid_submission_is_accepted_and_emails_queued(): void
    {
        Queue::fake();
        $this->mockEnrichment();

        $response = $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'email' => 'legit_' . uniqid() . '@realcompany.com',
        ]));

        $response->assertRedirect();
        $response->assertSessionHas('inquiry_success');

        $inquiry = Inquiry::latest()->first();
        $this->assertEquals('new', $inquiry->status);
        $this->assertEquals('low', $inquiry->spam_risk);
        $this->assertFalse((bool) $inquiry->honeypot_triggered);

        // Both welcome and admin emails should be queued
        Queue::assertCount(2);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SUCCESS MESSAGE IS SHOWN FOR REJECTIONS (silent)
    // ──────────────────────────────────────────────────────────────────────────

    public function test_rejected_submissions_receive_normal_success_message(): void
    {
        Queue::fake();
        $this->mockEnrichment(['ip_is_proxy' => true, 'email_type' => 'disposable']);

        $response = $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'email' => 'spam@mailinator.com',
        ]));

        // The user sees the standard thank-you message, not an error
        $response->assertSessionHas('inquiry_success');
        $response->assertSessionMissing('errors');
    }
}
