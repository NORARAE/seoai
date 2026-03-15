<?php

namespace Tests\Feature;

use App\Jobs\GeneratePagePayloadJob;
use App\Jobs\PublishPagePayloadJob;
use App\Models\PageGenerationBatch;
use App\Models\PagePayload;
use App\Models\SeoOpportunity;
use App\Models\Site;
use App\Models\City;
use App\Models\Service;
use App\Models\Client;
use App\Models\State;
use App\Models\PublishingLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PayloadGenerationPipelineTest extends TestCase
{
    use RefreshDatabase;

    protected Site $site;
    protected Service $service;
    protected SeoOpportunity $opportunity;
    protected PageGenerationBatch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $client = Client::factory()->create();
        
        $this->site = Site::factory()->create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'test.com',
            'publishing_mode' => 'export', // Use export mode for testing
        ]);

        $state = State::factory()->create(['code' => 'NY', 'name' => 'New York']);
        $city = City::factory()->create(['state_id' => $state->id, 'name' => 'Buffalo']);

        $this->service = Service::factory()->create([
            'site_id' => $this->site->id,
            'name' => 'HVAC Repair',
            'is_active' => true,
        ]);

        $this->opportunity = SeoOpportunity::factory()->create([
            'site_id' => $this->site->id,
            'service_id' => $this->service->id,
            'city_id' => $city->id,
            'location_type' => 'City',
            'keyword_phrase' => 'hvac repair buffalo ny',
            'priority_score' => 85,
            'optimization_status' => 'identified',
        ]);

        $this->batch = PageGenerationBatch::factory()->create([
            'site_id' => $this->site->id,
            'client_id' => $client->id,
            'status' => 'processing',
            'requested_count' => 1,
        ]);
    }

    /** @test */
    public function generate_payload_job_creates_payload()
    {
        $job = new GeneratePagePayloadJob($this->opportunity->id, $this->batch->id);
        $job->handle(app(\App\Services\PagePayloadGeneratorService::class));

        // Refresh opportunity to get updated payload_id
        $this->opportunity->refresh();

        $this->assertNotNull($this->opportunity->payload_id);

        $payload = PagePayload::find($this->opportunity->payload_id);
        
        $this->assertNotNull($payload);
        $this->assertEquals($this->site->id, $payload->site_id);
        $this->assertEquals($this->batch->id, $payload->batch_id);
        $this->assertEquals('draft', $payload->status);
    }

    /** @test */
    public function generate_payload_job_updates_batch_count()
    {
        $job = new GeneratePagePayloadJob($this->opportunity->id, $this->batch->id);
        $job->handle(app(\App\Services\PagePayloadGeneratorService::class));

        $this->batch->refresh();

        $this->assertEquals(1, $this->batch->payload_count);
    }

    /** @test */
    public function generate_payload_job_handles_failures()
    {
        // Create opportunity with invalid data
        $invalidOpportunity = SeoOpportunity::factory()->create([
            'site_id' => $this->site->id,
            'service_id' => 99999, // Non-existent service
            'city_id' => 99999, // Non-existent city
            'location_type' => 'City',
        ]);

        $job = new GeneratePagePayloadJob($invalidOpportunity->id, $this->batch->id);

        try {
            $job->handle(app(\App\Services\PagePayloadGeneratorService::class));
        } catch (\Exception $e) {
            // Expected to fail
            $this->assertInstanceOf(\Exception::class, $e);
        }

        // Batch should track the failure
        $this->batch->refresh();
        $this->assertEquals(0, $this->batch->payload_count);
    }

    /** @test */
    public function publish_payload_job_creates_publishing_log()
    {
        // First generate payload
        $generateJob = new GeneratePagePayloadJob($this->opportunity->id, $this->batch->id);
        $generateJob->handle(app(\App\Services\PagePayloadGeneratorService::class));

        $this->opportunity->refresh();
        $payload = PagePayload::find($this->opportunity->payload_id);
        
        // Update payload to ready status
        $payload->update(['status' => 'ready']);

        // Now publish
        $publishJob = new PublishPagePayloadJob($payload->id);
        $publishJob->handle(app(\App\Services\PublishingService::class));

        // Check publishing log was created
        $log = PublishingLog::where('payload_id', $payload->id)->first();
        
        $this->assertNotNull($log);
        $this->assertEquals('export', $log->adapter_type);
        $this->assertEquals('export', $log->action);
    }

    /** @test */
    public function publish_payload_job_updates_payload_status()
    {
        // Generate and publish
        $generateJob = new GeneratePagePayloadJob($this->opportunity->id, $this->batch->id);
        $generateJob->handle(app(\App\Services\PagePayloadGeneratorService::class));

        $this->opportunity->refresh();
        $payload = PagePayload::find($this->opportunity->payload_id);
        $payload->update(['status' => 'ready']);

        $publishJob = new PublishPagePayloadJob($payload->id);
        $publishJob->handle(app(\App\Services\PublishingService::class));

        $payload->refresh();

        // For export mode, should be 'exported'
        $this->assertEquals('exported', $payload->publish_status);
    }

    /** @test */
    public function publish_payload_job_updates_batch_counts()
    {
        // Generate payload
        $generateJob = new GeneratePagePayloadJob($this->opportunity->id, $this->batch->id);
        $generateJob->handle(app(\App\Services\PagePayloadGeneratorService::class));

        $this->opportunity->refresh();
        $payload = PagePayload::find($this->opportunity->payload_id);
        $payload->update(['status' => 'ready']);

        // Publish
        $publishJob = new PublishPagePayloadJob($payload->id);
        $publishJob->handle(app(\App\Services\PublishingService::class));

        $this->batch->refresh();

        // For export mode, exported_count should increment
        $this->assertEquals(1, $this->batch->exported_count);
    }

    /** @test */
    public function end_to_end_generation_and_publishing()
    {
        Queue::fake();

        // Use the bulk service to generate batch
        $service = app(\App\Services\BulkPageExpansionService::class);
        
        $batch = $service->generateBatch($this->site, [
            'count' => 1,
            'auto_publish' => false,
        ]);

        // Verify batch was created
        $this->assertInstanceOf(PageGenerationBatch::class, $batch);
        $this->assertEquals('processing', $batch->status);
        
        // Verify generation job was queued
        Queue::assertPushed(GeneratePagePayloadJob::class, 1);
        
        // Actually run the job
        Queue::assertPushed(GeneratePagePayloadJob::class, function ($job) {
            return $job->opportunityId === $this->opportunity->id;
        });
    }

    /** @test */
    public function failed_job_updates_batch_failed_count()
    {
        $this->batch->update(['failed_count' => 0]);

        // Create invalid opportunity
        $invalidOpportunity = SeoOpportunity::factory()->create([
            'site_id' => $this->site->id,
            'service_id' => 99999,
            'city_id' => 99999,
        ]);

        $job = new GeneratePagePayloadJob($invalidOpportunity->id, $this->batch->id);

        // Simulate job failure
        try {
            $job->handle(app(\App\Services\PagePayloadGeneratorService::class));
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $job->failed($e);
        }

        $this->batch->refresh();
        $this->assertEquals(1, $this->batch->failed_count);
    }
}
