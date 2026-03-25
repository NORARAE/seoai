<?php

namespace Tests\Feature\Api;

use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_invalid_key_when_license_does_not_exist(): void
    {
        $response = $this->postJson('/api/v1/validate', [
            'license_key' => 'MISS-ING0-KEY0-0000',
            'site_url' => 'https://example.com',
            'plugin_ver' => '1.2.0',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'valid' => false,
                'reason' => 'invalid_key',
            ]);

        $this->assertDatabaseHas('license_validations', [
            'license_key' => 'MISS-ING0-KEY0-0000',
            'result' => 'invalid',
        ]);
    }

    public function test_it_accepts_matching_domains_even_when_protocol_and_www_differ(): void
    {
        License::query()->create([
            'license_key' => 'ABCD-EFGH-IJKL-MNOP',
            'customer_email' => 'agency@example.com',
            'customer_name' => 'Agency Owner',
            'site_url' => 'clientsite.com',
            'plan' => 'scale',
            'urls_allowed' => 10000,
            'status' => 'active',
            'expires_at' => now()->addMonth(),
        ]);

        $response = $this->postJson('/api/v1/validate', [
            'license_key' => 'ABCD-EFGH-IJKL-MNOP',
            'site_url' => 'https://www.clientsite.com/some/page',
            'plugin_ver' => '1.2.0',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'valid' => true,
                'plan' => 'scale',
                'urls_allowed' => 10000,
            ]);

        $this->assertDatabaseHas('license_validations', [
            'license_key' => 'ABCD-EFGH-IJKL-MNOP',
            'site_url' => 'clientsite.com',
            'result' => 'valid',
        ]);
    }

    public function test_it_rejects_domain_mismatches(): void
    {
        License::query()->create([
            'license_key' => 'WXYZ-1234-ABCD-5678',
            'customer_email' => 'agency@example.com',
            'customer_name' => 'Agency Owner',
            'site_url' => 'allowed-site.com',
            'plan' => 'growth',
            'urls_allowed' => 2500,
            'status' => 'active',
            'expires_at' => now()->addMonth(),
        ]);

        $response = $this->postJson('/api/v1/validate', [
            'license_key' => 'WXYZ-1234-ABCD-5678',
            'site_url' => 'https://wrong-site.com',
            'plugin_ver' => '1.2.0',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'valid' => false,
                'reason' => 'domain_mismatch',
            ]);
    }

    public function test_it_expires_trials_on_validation_when_past_due(): void
    {
        $license = License::query()->create([
            'license_key' => 'TRIA-LKEY-EXPR-0001',
            'customer_email' => 'agency@example.com',
            'customer_name' => 'Agency Owner',
            'site_url' => 'trial-site.com',
            'plan' => 'starter',
            'urls_allowed' => 500,
            'status' => 'trial',
            'trial_ends_at' => now()->subDay(),
        ]);

        $response = $this->postJson('/api/v1/validate', [
            'license_key' => $license->license_key,
            'site_url' => 'http://trial-site.com',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'valid' => false,
                'reason' => 'trial_expired',
            ]);

        $this->assertDatabaseHas('licenses', [
            'id' => $license->id,
            'status' => 'expired',
        ]);
    }
}