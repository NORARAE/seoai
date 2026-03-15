<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PagePayload;
use App\Models\PageGenerationBatch;
use App\Models\PublishingLog;
use App\Models\Site;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlatformAgnosticArchitectureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function page_payload_can_be_created()
    {
        $client = Client::create(['name' => 'Test Client']);
        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'example.com',
            'status' => 'active',
        ]);
        
        $payload = PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'title' => 'Test Service Page',
            'slug' => 'test-service-page',
            'meta_description' => 'Test meta description',
            'body_content' => '<p>Test content</p>',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('page_payloads', [
            'title' => 'Test Service Page',
            'slug' => 'test-service-page',
        ]);
        
        $this->assertEquals('Test Service Page', $payload->title);
    }

    /** @test */
    public function page_payload_can_export_to_json()
    {
        $client = Client::create(['name' => 'Test Client']);
        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'example.com',
            'status' => 'active',
        ]);
        
        $payload = PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'title' => 'Export Test',
            'slug' => 'export-test',
            'body_content' => '<p>Content</p>',
            'status' => 'draft',
        ]);

        $export = $payload->toExportFormat('json');
        
        $this->assertIsString($export);
        $data = json_decode($export, true);
        $this->assertEquals('Export Test', $data['content']['title']);
        $this->assertEquals('export-test', $data['content']['slug']);
    }

    /** @test */
    public function page_payload_can_export_to_markdown()
    {
        $client = Client::create(['name' => 'Test Client']);
        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'example.com',
            'status' => 'active',
        ]);
        
        $payload = PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'title' => 'Markdown Test',
            'slug' => 'markdown-test',
            'meta_description' => 'Test description',
            'body_content' => '<p>Test content</p>',
            'status' => 'draft',
        ]);

        $markdown = $payload->toExportFormat('markdown');
        
        $this->assertStringContainsString('# Markdown Test', $markdown);
        $this->assertStringContainsString('Test description', $markdown);
        $this->assertStringContainsString('Test content', $markdown);
    }

    /** @test */
    public function page_generation_batch_tracks_progress()
    {
        $client = Client::create(['name' => 'Test Client']);
        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'example.com',
            'status' => 'active',
        ]);
        
        $batch = PageGenerationBatch::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'status' => 'processing',
            'requested_count' => 10,
            'payload_count' => 0,
            'published_count' => 0,
        ]);

        $this->assertEquals(0, $batch->getPayloadProgressPercentage());
        
        $batch->incrementPayload();
        $batch->incrementPayload();
        $batch->incrementPayload();
        
        $this->assertEquals(3, $batch->payload_count);
        $this->assertEquals(30, $batch->getPayloadProgressPercentage());
    }

    /** @test */
    public function publishing_log_records_publishing_attempts()
    {
        $client = Client::create(['name' => 'Test Client']);
        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'example.com',
            'status' => 'active',
            'cms_type' => 'wordpress',
            'publishing_mode' => 'native',
        ]);
        
        $payload = PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'title' => 'Test Page',
            'slug' => 'test-page',
            'body_content' => '<p>Content</p>',
            'status' => 'draft',
        ]);

        $log = PublishingLog::create([
            'payload_id' => $payload->id,
            'site_id' => $site->id,
            'client_id' => $client->id,
            'adapter_type' => 'wordpress',
            'action' => 'publish',
            'result' => 'success',
            'remote_id' => '123',
        ]);

        $this->assertDatabaseHas('publishing_logs', [
            'payload_id' => $payload->id,
            'result' => 'success',
        ]);
        
        $this->assertEquals('wordpress', $log->adapter_type);
    }

    /** @test */
    public function site_has_cms_configuration_fields()
    {
        $client = Client::create(['name' => 'Test Client']);
        
        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'example.com',
            'status' => 'active',
            'cms_type' => 'wordpress',
            'publishing_mode' => 'native',
            'publishing_status' => 'connected',
            'wordpress_url' => 'https://example.com',
        ]);

        $this->assertEquals('wordpress', $site->cms_type);
        $this->assertEquals('native', $site->publishing_mode);
        $this->assertEquals('connected', $site->publishing_status);
        $this->assertEquals('https://example.com', $site->wordpress_url);
    }

    /** @test */
    public function page_payload_belongs_to_batch()
    {
        $client = Client::create(['name' => 'Test Client']);
        $site = Site::create([
            'client_id' => $client->id,
            'name' => 'Test Site',
            'domain' => 'example.com',
            'status' => 'active',
        ]);
        
        $batch = PageGenerationBatch::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'status' => 'processing',
            'requested_count' => 5,
        ]);

        $payload = PagePayload::create([
            'site_id' => $site->id,
            'client_id' => $client->id,
            'batch_id' => $batch->id,
            'title' => 'Batch Test',
            'slug' => 'batch-test',
            'body_content' => '<p>Content</p>',
            'status' => 'draft',
        ]);

        $this->assertEquals($batch->id, $payload->batch_id);
        $this->assertInstanceOf(PageGenerationBatch::class, $payload->batch);
        $this->assertEquals(1, $batch->payloads()->count());
    }
}
