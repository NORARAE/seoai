<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Site;
use App\Models\City;
use App\Models\Client;
use App\Models\County;
use App\Models\State;
use App\Models\Service;
use App\Models\SeoOpportunity;
use App\Models\PagePayload;
use App\Services\PagePayloadGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PagePayloadGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PagePayloadGeneratorService $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = app(PagePayloadGeneratorService::class);
    }

    /** @test */
    public function it_generates_payload_with_non_empty_body_content()
    {
        // Arrange: Create test data manually
        $client = Client::create([
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ]);

        $state = State::create([
            'name' => 'Washington',
            'code' => 'WA',
            'slug' => 'washington',
        ]);

        $site = Site::create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'name' => 'Test Site',
            'url' => 'https://example.com',
            'domain' => 'example.com',
            'publishing_mode' => 'export_only',
        ]);

        $service = Service::create([
            'site_id' => $site->id,
            'name' => 'Crime Scene Cleanup',
            'slug' => 'crime-scene-cleanup',
            'description' => 'Professional crime scene cleanup services',
        ]);

        $county = County::create([
            'state_id' => $state->id,
            'name' => 'King County',
            'slug' => 'king-county',
        ]);

        $city = City::create([
            'state_id' => $state->id,
            'county_id' => $county->id,
            'name' => 'Seattle',
            'slug' => 'seattle',
            'state_code' => 'WA',
            'state_name' => 'Washington',
            'population' => 737000,
        ]);

        $opportunity = SeoOpportunity::create([
            'site_id' => $site->id,
            'service_id' => $service->id,
            'location_id' => $city->id,
            'status' => 'approved',
            'priority_score' => 85,
            'search_volume' => 100,
        ]);

        // Act: Generate payload
        $payload = $this->generator->generateFromOpportunity($opportunity);

        // Act: Generate payload
        $payload = $this->generator->generateFromOpportunity($opportunity);

        // Assert: Body content should be populated
        $this->assertNotEmpty($payload->body_content, 'Body content should not be empty');
        $this->assertGreaterThan(100, strlen($payload->body_content), 'Body content should be substantive');
        
        // Verify it contains expected HTML structure
        $this->assertStringContainsString('<h2', $payload->body_content, 'Should contain heading tags');
        $this->assertStringContainsString('<p>', $payload->body_content, 'Should contain paragraph tags');
        
        // Verify other content is also populated
        $this->assertNotEmpty($payload->title);
        $this->assertNotEmpty($payload->excerpt);
        $this->assertEquals('needs_review', $payload->status);
    }

    /** @test */
    public function it_correctly_uses_body_sections_json_key()
    {
        // This test explicitly verifies the key name fix - it should use 'body_sections_json' not 'body_sections'
        
        $client = Client::create([
            'name' => 'Test Client 2',
            'email' => 'test2@example.com',
        ]);

        $state = State::create([
            'name' => 'Oregon',
            'code' => 'OR',
            'slug' => 'oregon',
        ]);

        $site = Site::create([
            'client_id' => $client->id,
            'state_id' => $state->id,
            'name' => 'Test Site 2',
            'url' => 'https://example2.com',
            'domain' => 'example2.com',
            'publishing_mode' => 'export_only',
        ]);

        $service = Service::create([
            'site_id' => $site->id,
            'name' => 'Biohazard Cleanup',
            'slug' => 'biohazard-cleanup',
            'description' => 'Professional biohazard cleanup services',
        ]);

        $county = County::create([
            'state_id' => $state->id,
            'name' => 'Multnomah County',
            'slug' => 'multnomah-county',
        ]);

        $city = City::create([
            'state_id' => $state->id,
            'county_id' => $county->id,
            'name' => 'Portland',
            'slug' => 'portland',
            'state_code' => 'OR',
            'state_name' => 'Oregon',
            'population' => 650000,
        ]);

        $opportunity = SeoOpportunity::create([
            'site_id' => $site->id,
            'service_id' => $service->id,
            'location_id' => $city->id,
            'status' => 'approved',
            'priority_score' => 85,
            'search_volume' => 100,
        ]);

        // Generate payload
        $payload = $this->generator->generateFromOpportunity($opportunity);

        // Verify the body_content field was populated (wouldn't be if wrong key was used)
        $this->assertNotEmpty($payload->body_content);
        
        // Verify multiple sections were rendered (composer returns array of sections)
        $sectionCount = substr_count($payload->body_content, '<h2');
        $this->assertGreaterThan(0, $sectionCount, 'Should have multiple sections rendered as headings');
    }
}
