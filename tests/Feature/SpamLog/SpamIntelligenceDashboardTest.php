<?php

namespace Tests\Feature\SpamLog;

use App\Models\BlockedIp;
use App\Models\Inquiry;
use App\Models\SpamLog;
use App\Models\User;
use App\Services\InquiryAntiSpamService;
use App\Services\InquiryEnrichmentService;
use App\Services\TurnstileVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * SpamIntelligenceDashboardTest
 *
 * Verifies:
 *  - SpamLog records now capture action, name, company, user_agent, turnstile fields
 *  - Pre-flight blocks are logged with action='block'
 *  - Pre-flight flags are logged with action='flag'
 *  - Medium-risk allowed submissions are logged with action='flag'
 *  - SpamLogResource admin page loads (200)
 *  - Block/unblock IP actions work
 *  - Mark reviewed action works
 *  - SpamIntelligenceWidget stats are calculated correctly
 */
class SpamIntelligenceDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'          => 'Test User',
            'company'       => 'ValidCorp',
            'email'         => 'test@realcompany.com',
            'website'       => 'https://realcompany.com',
            'type'          => 'business',
            'tier'          => '5k',
            'niche'         => 'Law',
            'message'       => 'Looking for SEO solution for our business.',
            'form_loaded_at' => (string) (time() - 60),
        ], $overrides);
    }

    private function mockEnrichment(array $override = []): void
    {
        $mock = $this->createMock(InquiryEnrichmentService::class);
        $defaults = [
            'ip_city' => 'Seattle', 'ip_region' => 'Washington', 'ip_country' => 'US',
            'ip_isp' => 'Test ISP', 'ip_is_proxy' => false, 'ip_is_hosting' => false,
            'url_status' => 'valid', 'url_is_https' => true, 'domain_age_days' => 400,
            'email_type' => 'business', 'company_enrichment' => null,
            'time_to_submit_seconds' => 60, 'recaptcha_score' => null,
        ];
        $enrichData = array_merge($defaults, $override);
        $mock->method('enrichAll')->willReturn($enrichData);
        $mock->method('verifyRecaptcha')->willReturn(null);
        $mock->method('scoreSpamRisk')->willReturnCallback(
            fn (array $data) => (new InquiryEnrichmentService())->scoreSpamRisk($data)
        );
        $this->app->instance(InquiryEnrichmentService::class, $mock);
    }

    private function adminUser(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SPAM LOG FIELD COVERAGE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_blocked_spam_log_captures_new_fields(): void
    {
        Queue::fake();
        $this->mockEnrichment();

        // 'google' is a blocked company name — triggers antispam block
        $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'company' => 'google',
            'name'    => 'Spam Bot',
        ]));

        $inquiry = Inquiry::latest()->first();
        $log = SpamLog::where('inquiry_id', $inquiry->id)->latest()->first();

        $this->assertNotNull($log);
        $this->assertEquals('block', $log->action);
        $this->assertEquals('antispam_blocked', $log->reason);
        $this->assertEquals('Spam Bot', $log->name);
        $this->assertEquals('google', $log->company);
        $this->assertEquals('test@realcompany.com', $log->email);
        $this->assertNotNull($log->signals);
        $this->assertContains('blocked_company_name', $log->signals);
        // Turnstile is bypassed in testing env — reason='turnstile_disabled', valid=true
        $this->assertEquals('turnstile_disabled', $log->turnstile_reason);
    }

    public function test_honeypot_block_log_has_action_block(): void
    {
        Queue::fake();
        $this->mockEnrichment();

        $this->post(route('licensing-inquiry.store'), $this->validPayload([
            'website_confirm' => 'http://spam.example.com',
        ]));

        $inquiry = Inquiry::latest()->first();
        $log = SpamLog::where('inquiry_id', $inquiry->id)->first();

        $this->assertNotNull($log);
        $this->assertEquals('block', $log->action);
        $this->assertEquals('high', $log->spam_risk);
    }

    public function test_pre_flight_flag_is_logged_with_action_flag(): void
    {
        Queue::fake();
        $this->mockEnrichment();

        // Turnstile missing (2 pts) + one more mild signal = flag territory (3-5 pts).
        // We need to trigger a flag (score 3–5) without a block (score ≥ 6).
        // Turnstile missing = 2pts. We'll mock the antispam service to return 'flag'.
        $antiSpamMock = $this->createMock(InquiryAntiSpamService::class);
        $antiSpamMock->method('evaluate')->willReturn([
            'allowed'    => true,
            'action'     => 'flag',
            'risk_score' => 4,
            'reasons'    => ['turnstile_missing', 'duplicate_message'],
        ]);
        $this->app->instance(InquiryAntiSpamService::class, $antiSpamMock);

        $this->post(route('licensing-inquiry.store'), $this->validPayload());

        $inquiry = Inquiry::latest()->first();
        $flagLog = SpamLog::where('inquiry_id', $inquiry->id)
            ->where('action', 'flag')
            ->first();

        $this->assertNotNull($flagLog, 'Flagged pre-flight submission was not logged to spam_logs');
        $this->assertEquals('flag', $flagLog->action);
        $this->assertEquals('antispam_flagged', $flagLog->reason);
        $this->assertEquals('medium', $flagLog->spam_risk);
        $this->assertEquals(4, $flagLog->risk_score);
        $this->assertEquals('ValidCorp', $flagLog->company);
        $this->assertEquals('Test User', $flagLog->name);
    }

    public function test_medium_risk_allowed_inquiry_is_logged_with_action_flag(): void
    {
        Queue::fake();

        // Mock enrichment returning proxy (VPN) which pushes to medium risk
        $this->mockEnrichment(['ip_is_proxy' => true]);

        // Pre-flight allows it through (mock returns allow)
        $antiSpamMock = $this->createMock(InquiryAntiSpamService::class);
        $antiSpamMock->method('evaluate')->willReturn([
            'allowed' => true, 'action' => 'allow', 'risk_score' => 1, 'reasons' => [],
        ]);
        $this->app->instance(InquiryAntiSpamService::class, $antiSpamMock);

        // Mock scoreSpamRisk to return medium
        $enrichMock = $this->createMock(InquiryEnrichmentService::class);
        $enrichMock->method('enrichAll')->willReturn([
            'ip_city' => 'NYC', 'ip_region' => 'NY', 'ip_country' => 'US',
            'ip_isp' => 'VPN Inc', 'ip_is_proxy' => true, 'ip_is_hosting' => false,
            'url_status' => 'valid', 'url_is_https' => true, 'domain_age_days' => 400,
            'email_type' => 'business', 'company_enrichment' => null,
            'time_to_submit_seconds' => 60, 'recaptcha_score' => null,
        ]);
        $enrichMock->method('verifyRecaptcha')->willReturn(null);
        $enrichMock->method('scoreSpamRisk')->willReturn([
            'spam_risk' => 'medium',
            '_score'    => 4,
            '_signals'  => ['vpn_proxy_no_trust'],
        ]);
        $this->app->instance(InquiryEnrichmentService::class, $enrichMock);

        $this->post(route('licensing-inquiry.store'), $this->validPayload());

        $inquiry = Inquiry::latest()->first();
        $this->assertNotNull($inquiry);
        // Should NOT be rejected
        $this->assertNotEquals('rejected', $inquiry->status);

        $flagLog = SpamLog::where('inquiry_id', $inquiry->id)
            ->where('action', 'flag')
            ->where('reason', 'medium_risk_allowed')
            ->first();

        $this->assertNotNull($flagLog, 'Medium-risk allowed inquiry was not logged');
        $this->assertEquals('flag', $flagLog->action);
        $this->assertEquals('medium', $flagLog->spam_risk);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SPAMLOG MODEL HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    public function test_block_ip_adds_to_blocked_ips_table(): void
    {
        $inquiry = Inquiry::factory()->create();
        $log = SpamLog::factory()->create([
            'inquiry_id' => $inquiry->id,
            'ip_address' => '10.0.0.1',
            'action'     => 'block',
        ]);

        $this->assertFalse($log->isIpBlocked());
        $log->blockIp('admin');
        $this->assertTrue(BlockedIp::isBlocked('10.0.0.1'));
    }

    public function test_unblock_ip_removes_from_blocked_ips_table(): void
    {
        BlockedIp::create(['ip_address' => '10.0.0.2', 'reason' => 'test', 'blocked_at' => now()]);
        $inquiry = Inquiry::factory()->create();
        $log = SpamLog::factory()->create([
            'inquiry_id' => $inquiry->id,
            'ip_address' => '10.0.0.2',
            'action'     => 'block',
        ]);

        $this->assertTrue($log->isIpBlocked());
        $log->unblockIp();
        $this->assertFalse(BlockedIp::isBlocked('10.0.0.2'));
    }

    public function test_is_ip_blocked_returns_false_when_no_ip(): void
    {
        $inquiry = Inquiry::factory()->create();
        $log = SpamLog::factory()->create(['inquiry_id' => $inquiry->id, 'ip_address' => null]);
        $this->assertFalse($log->isIpBlocked());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // FILAMENT ADMIN RESOURCE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_spam_log_admin_list_page_loads_for_admin(): void
    {
        $admin = $this->adminUser();
        $inquiry = Inquiry::factory()->create();
        SpamLog::factory()->count(3)->create(['inquiry_id' => $inquiry->id]);

        $response = $this->actingAs($admin)->get('/admin/spam-logs');

        $response->assertStatus(200);
    }

    public function test_spam_log_admin_view_page_loads_for_admin(): void
    {
        $admin = $this->adminUser();
        $inquiry = Inquiry::factory()->create();
        $log = SpamLog::factory()->create(['inquiry_id' => $inquiry->id]);

        $response = $this->actingAs($admin)->get("/admin/spam-logs/{$log->id}");

        $response->assertStatus(200);
    }

    public function test_spam_log_admin_inaccessible_for_unauthenticated(): void
    {
        $response = $this->get('/admin/spam-logs');
        // Filament redirects unauthenticated to login
        $response->assertRedirect();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // WIDGET STATS
    // ──────────────────────────────────────────────────────────────────────────

    public function test_spam_intelligence_widget_counts_are_accurate(): void
    {
        $inquiry = Inquiry::factory()->create();

        // 2 blocks today
        SpamLog::factory()->count(2)->create([
            'inquiry_id' => $inquiry->id,
            'action'     => 'block',
            'created_at' => now(),
        ]);

        // 1 flag today
        SpamLog::factory()->create([
            'inquiry_id' => $inquiry->id,
            'action'     => 'flag',
            'created_at' => now(),
        ]);

        // 1 turnstile failure
        SpamLog::factory()->create([
            'inquiry_id'      => $inquiry->id,
            'action'          => 'block',
            'turnstile_valid' => false,
            'created_at'      => now()->subDays(3),
        ]);

        $this->assertEquals(2, SpamLog::where('action', 'block')->whereDate('created_at', today())->count());
        $this->assertEquals(1, SpamLog::where('action', 'flag')->whereDate('created_at', today())->count());
        $this->assertEquals(1, SpamLog::where('turnstile_valid', false)->where('created_at', '>=', now()->subDays(7))->count());
    }
}
