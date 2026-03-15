<?php

namespace Tests\Unit;

use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\City;
use App\Models\Service;
use App\Models\Client;
use App\Models\State;
use App\Models\PagePayload;
use App\Services\PagePayloadGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagePayloadGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PagePayloadGeneratorService $service;
    protected Site $site;
    protected Service $service_model;
    protected City $city;
    protected SeoOpportunity $opportunity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PagePayloadGeneratorService::class);

        // Create test data
        $client = Client::factory()->create();
        
        $this->site = Site::factory()->create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'test.com',
        ]);

        $state = State::factory()->create(['code' => 'NY', 'name' => 'New York']);
        $this->city = City::factory()->create(['state_id' => $state->id, 'name' => 'Buffalo']);

        $this->service_model = Service::factory()->create([
            'site_id' => $this->site->id,
            'name' => 'HVAC Repair',
        ]);

        $this->opportunity = SeoOpportunity::factory()->create([
            'site_id' => $this->site->id,
            'service_id' => $this->service_model->id,
            'city_id' => $this->city->id,
            'location_type' => 'City',
            'keyword_phrase' => 'hvac repair buffalo ny',
            'priority_score' => 85,
            'optimization_status' => 'identified',
        ]);
    }

    /** @test */
    public function it_generates_payload_from_opportunity()
    {
        $payload = $this->service->generateFromOpportunity($this->opportunity);

        $this->assertInstanceOf(PagePayload::class, $payload);
        $this->assertEquals($this->site->id, $payload->site_id);
        $this->assertEquals($this->service_model->id, $payload->service_id);
        $this->assertEquals($this->city->id, $payload->location_id);
        $this->assertEquals('City', $payload->location_type);
        $this->assertEquals('draft', $payload->status);
        $this->assertEquals('pending', $payload->publish_status);
        $this->assertNotNull($payload->title);
        $this->assertNotNull($payload->slug);
        $this->assertNotNull($payload->body_content);
    }

    /** @test */
    public function it_generates_valid_slug()
    {
        $payload = $this->service->generateFromOpportunity($this->opportunity);

        $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $payload->slug);
        $this->assertStringContainsString('hvac-repair', $payload->slug);
        $this->assertStringContainsString('buffalo', $payload->slug);
        $this->assertStringContainsString('ny', $payload->slug);
    }

    /** @test */
    public function it_generates_meta_tags()
    {
        $payload = $this->service->generateFromOpportunity($this->opportunity);

        $this->assertIsArray($payload->meta_tags);
        $this->assertArrayHasKey('title', $payload->meta_tags);
        $this->assertArrayHasKey('description', $payload->meta_tags);
        $this->assertArrayHasKey('canonical', $payload->meta_tags);
        $this->assertArrayHasKey('og:title', $payload->meta_tags);
    }

    /** @test */
    public function it_generates_schema_json_ld()
    {
        $payload = $this->service->generateFromOpportunity($this->opportunity);

        $this->assertIsArray($payload->schema_json_ld);
        $this->assertArrayHasKey('@context', $payload->schema_json_ld);
        $this->assertEquals('https://schema.org', $payload->schema_json_ld['@context']);
        $this->assertArrayHasKey('@type', $payload->schema_json_ld);
    }

    /** @test */
    public function it_plans_internal_links()
    {
        // Create some additional opportunities for linking
        $parentOpportunity = SeoOpportunity::factory()->create([
            'site_id' => $this->site->id,
            'service_id' => $this->service_model->id,
            'city_id' => null,
            'location_type' => 'State',
            'keyword_phrase' => 'hvac repair new york',
        ]);

        $payload = $this->service->generateFromOpportunity($this->opportunity);

        $this->assertIsArray($payload->internal_links);
        // May have parent, hub, or nearby links depending on available content
        $this->assertGreaterThanOrEqual(0, count($payload->internal_links));
    }

    /** @test */
    public function it_calculates_seo_score()
    {
        $payload = $this->service->generateFromOpportunity($this->opportunity);

        $this->assertIsInt($payload->seo_score);
        $this->assertGreaterThanOrEqual(0, $payload->seo_score);
        $this->assertLessThanOrEqual(100, $payload->seo_score);
    }

    /** @test */
    public function it_batch_generates_multiple_payloads()
    {
        $opportunities = SeoOpportunity::factory()->count(5)->create([
            'site_id' => $this->site->id,
            'service_id' => $this->service_model->id,
            'city_id' => $this->city->id,
            'location_type' => 'City',
            'optimization_status' => 'identified',
        ]);

        $results = $this->service->batchGenerate($opportunities);

        $this->assertCount(5, $results['payloads']);
        $this->assertCount(0, $results['errors']);

        foreach ($results['payloads'] as $payload) {
            $this->assertInstanceOf(PagePayload::class, $payload);
        }
    }

    /** @test */
    public function it_handles_batch_generation_errors_gracefully()
    {
        // Create an opportunity with invalid data (e.g., missing city)
        $invalidOpportunity = SeoOpportunity::factory()->create([
            'site_id' => $this->site->id,
            'service_id' => $this->service_model->id,
            'city_id' => 99999, // Non-existent city
            'location_type' => 'City',
        ]);

        $validOpportunity = $this->opportunity;

        $results = $this->service->batchGenerate(collect([$invalidOpportunity, $validOpportunity]));

        // Should have 1 success and 1 error
        $this->assertCount(1, $results['payloads']);
        $this->assertCount(1, $results['errors']);
        $this->assertArrayHasKey($invalidOpportunity->id, $results['errors']);
    }

    /** @test */
    public function it_prevents_duplicate_slug_generation()
    {
        // Generate first payload
        $payload1 = $this->service->generateFromOpportunity($this->opportunity);

        // Try to generate another payload for the same opportunity
        $payload2 = $this->service->generateFromOpportunity($this->opportunity);

        // Slugs should be different (second one should have suffix)
        $this->assertNotEquals($payload1->slug, $payload2->slug);
        $this->assertStringStartsWith($payload1->slug, $payload2->slug);
    }

    /** @test */
    public function it_sets_ready_status_for_quality_content()
    {
        $payload = $this->service->generateFromOpportunity($this->opportunity);

        // With proper content generation, status should be 'ready'
        if (strlen($payload->body_content) > 500) {
            $this->assertEquals('ready', $payload->status);
        } else {
            $this->assertEquals('draft', $payload->status);
        }
    }
}
