<?php

namespace Tests\Unit;

use App\Models\PageGenerationBatch;
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\City;
use App\Models\Service;
use App\Models\Client;
use App\Models\State;
use App\Models\PagePayload;
use App\Services\BulkPageExpansionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GeneratePagePayloadJob;
use Tests\TestCase;

class BulkPageExpansionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BulkPageExpansionService $service;
    protected Site $site;
    protected Service $service_model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(BulkPageExpansionService::class);

        // Create test data
        $client = Client::factory()->create();
        
        $this->site = Site::factory()->create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'test.com',
            'publishing_mode' => 'native',
        ]);

        $state = State::factory()->create(['code' => 'NY', 'name' => 'New York']);
        $city = City::factory()->create(['state_id' => $state->id, 'name' => 'Buffalo']);

        $this->service_model = Service::factory()->create([
            'site_id' => $this->site->id,
            'name' => 'HVAC Repair',
            'is_active' => true,
        ]);

        // Create opportunities
        SeoOpportunity::factory()->count(10)->create([
            'site_id' => $this->site->id,
            'service_id' => $this->service_model->id,
            'city_id' => $city->id,
            'location_type' => 'City',
            'optimization_status' => 'identified',
            'priority_score' => 80,
        ]);
    }

    /** @test */
    public function it_creates_batch_with_correct_count()
    {
        Queue::fake();

        $batch = $this->service->generateBatch($this->site, [
            'count' => 5,
            'auto_publish' => false,
        ]);

        $this->assertInstanceOf(PageGenerationBatch::class, $batch);
        $this->assertEquals($this->site->id, $batch->site_id);
        $this->assertEquals(5, $batch->requested_count);
        $this->assertEquals('processing', $batch->status);
        
        Queue::assertPushed(GeneratePagePayloadJob::class, 5);
    }

    /** @test */
    public function it_enforces_max_batch_size()
    {
        Queue::fake();

        $batch = $this->service->generateBatch($this->site, [
            'count' => 100, // Should be capped at 50
        ]);

        $this->assertEquals(50, $batch->requested_count);
        
        // Only 10 jobs should be dispatched (we created 10 opportunities)
        Queue::assertPushed(GeneratePagePayloadJob::class, 10);
    }

    /** @test */
    public function it_filters_opportunities_by_service()
    {
        Queue::fake();

        // Create another service with opportunities
        $otherService = Service::factory()->create([
            'site_id' => $this->site->id,
            'name' => 'Plumbing',
            'is_active' => true,
        ]);

        $state = State::first();
        $city = City::first();

        SeoOpportunity::factory()->count(5)->create([
            'site_id' => $this->site->id,
            'service_id' => $otherService->id,
            'city_id' => $city->id,
            'location_type' => 'City',
            'optimization_status' => 'identified',
        ]);

        $batch = $this->service->generateBatch($this->site, [
            'count' => 20,
            'filters' => ['service_id' => $this->service_model->id],
        ]);

        // Should only dispatch 10 jobs (for HVAC service)
        Queue::assertPushed(GeneratePagePayloadJob::class, 10);
    }

    /** @test */
    public function it_prevents_duplicate_payloads()
    {
        Queue::fake();

        // Create an opportunity
        $opportunity = SeoOpportunity::first();

        // Create a payload for this opportunity
        PagePayload::factory()->create([
            'site_id' => $this->site->id,
            'service_id' => $opportunity->service_id,
            'location_id' => $opportunity->city_id,
            'location_type' => 'City',
            'slug' => 'hvac-repair-buffalo-ny',
        ]);

        // Try to generate batch - should skip the opportunity with existing payload
        $batch = $this->service->generateBatch($this->site, [
            'count' => 10,
            'skip_existing' => true,
        ]);

        // Should dispatch 9 jobs (10 opportunities - 1 existing)
        Queue::assertPushed(GeneratePagePayloadJob::class, 9);
    }

    /** @test */
    public function it_chains_publishing_when_auto_publish_enabled()
    {
        Queue::fake();

        $batch = $this->service->generateBatch($this->site, [
            'count' => 3,
            'auto_publish' => true,
        ]);

        $this->assertTrue($batch->auto_publish);
        
        Queue::assertPushed(GeneratePagePayloadJob::class, 3);
    }

    /** @test */
    public function it_publishes_batch_payloads()
    {
        Queue::fake();

        // Create batch
        $batch = PageGenerationBatch::factory()->create([
            'site_id' => $this->site->id,
            'status' => 'completed',
        ]);

        // Create payloads
        PagePayload::factory()->count(3)->create([
            'batch_id' => $batch->id,
            'site_id' => $this->site->id,
            'status' => 'ready',
            'publish_status' => 'pending',
        ]);

        $dispatched = $this->service->publishBatch($batch);

        $this->assertEquals(3, $dispatched);
        
        Queue::assertPushed(\App\Jobs\PublishPagePayloadJob::class, 3);
    }

    /** @test */
    public function it_skips_already_published_payloads()
    {
        Queue::fake();

        $batch = PageGenerationBatch::factory()->create([
            'site_id' => $this->site->id,
        ]);

        PagePayload::factory()->count(2)->create([
            'batch_id' => $batch->id,
            'site_id' => $this->site->id,
            'status' => 'ready',
            'publish_status' => 'pending',
        ]);

        PagePayload::factory()->create([
            'batch_id' => $batch->id,
            'site_id' => $this->site->id,
            'status' => 'ready',
            'publish_status' => 'published',
        ]);

        $dispatched = $this->service->publishBatch($batch);

        // Should only dispatch 2 jobs (skip the published one)
        $this->assertEquals(2, $dispatched);
    }

    /** @test */
    public function it_force_publishes_all_payloads()
    {
        Queue::fake();

        $batch = PageGenerationBatch::factory()->create([
            'site_id' => $this->site->id,
        ]);

        PagePayload::factory()->count(3)->create([
            'batch_id' => $batch->id,
            'site_id' => $this->site->id,
            'status' => 'ready',
            'publish_status' => 'published',
        ]);

        $dispatched = $this->service->publishBatch($batch, true);

        // With force=true, should dispatch all 3
        $this->assertEquals(3, $dispatched);
    }

    /** @test */
    public function it_exports_batch_to_zip()
    {
        $batch = PageGenerationBatch::factory()->create([
            'site_id' => $this->site->id,
        ]);

        PagePayload::factory()->count(3)->create([
            'batch_id' => $batch->id,
            'site_id' => $this->site->id,
        ]);

        $exportPath = $this->service->exportBatch($batch, 'json');

        $this->assertNotNull($exportPath);
        $this->assertStringContainsString('.zip', $exportPath);
        
        // Verify file exists
        $this->assertFileExists(storage_path('app/' . $exportPath));
    }

    /** @test */
    public function it_completes_batch()
    {
        $batch = PageGenerationBatch::factory()->create([
            'site_id' => $this->site->id,
            'status' => 'processing',
        ]);

        $this->service->completeBatch($batch);

        $batch->refresh();
        
        $this->assertEquals('completed', $batch->status);
        $this->assertNotNull($batch->completed_at);
    }

    /** @test */
    public function it_cancels_batch()
    {
        $batch = PageGenerationBatch::factory()->create([
            'site_id' => $this->site->id,
            'status' => 'processing',
        ]);

        $this->service->cancelBatch($batch);

        $batch->refresh();
        
        $this->assertEquals('failed', $batch->status);
        $this->assertNotNull($batch->completed_at);
    }

    /** @test */
    public function it_validates_opportunities_have_active_services()
    {
        Queue::fake();

        // Deactivate the service
        $this->service_model->update(['is_active' => false]);

        $batch = $this->service->generateBatch($this->site, ['count' => 10]);

        // Should dispatch 0 jobs (service is inactive)
        Queue::assertNotPushed(GeneratePagePayloadJob::class);
    }
}
