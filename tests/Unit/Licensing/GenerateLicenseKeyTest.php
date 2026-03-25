<?php

namespace Tests\Unit\Licensing;

use App\Actions\Licensing\GenerateLicenseKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateLicenseKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_formatted_license_key(): void
    {
        $key = app(GenerateLicenseKey::class)();

        $this->assertMatchesRegularExpression('/^[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}$/', $key);
    }
}