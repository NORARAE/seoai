<?php

namespace Tests\Unit\Services\Discovery;

use App\Models\InternalLink;
use App\Models\Site;
use App\Models\SiteCrawlSetting;
use App\Models\UrlInventory;
use App\Services\Discovery\CrawlQueueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class InternalLinkExtractionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deduplicates_source_target_edges_and_skips_self_links(): void
    {
        $site = Site::create([
            'domain' => 'example.com',
            'name' => 'Example',
        ]);

        SiteCrawlSetting::create([
            'site_id' => $site->id,
            'max_pages' => 100,
            'crawl_delay' => 1,
            'max_depth' => 4,
            'obey_robots' => true,
            'follow_nofollow' => false,
        ]);

        $source = UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/source',
            'normalized_url' => 'https://example.com/source',
            'path' => '/source',
            'status' => 'completed',
            'indexability_status' => 'indexable',
            'page_type' => 'other',
        ]);

        $target = UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/target',
            'normalized_url' => 'https://example.com/target',
            'path' => '/target',
            'status' => 'queued',
            'indexability_status' => 'unknown',
            'page_type' => 'other',
        ]);

        $service = app(CrawlQueueService::class);

        $method = new ReflectionMethod(CrawlQueueService::class, 'storeDiscoveredLinks');
        $method->setAccessible(true);

        $method->invoke($service, $site, $source, [
            ['url' => '/target', 'anchor_text' => 'One', 'rel' => null],
            ['url' => '/target', 'anchor_text' => 'Two', 'rel' => null],
            ['url' => '/source', 'anchor_text' => 'Self', 'rel' => null],
        ], 1);

        $this->assertSame(1, InternalLink::where('site_id', $site->id)->count());

        $source->refresh();
        $target->refresh();

        $this->assertSame(1, $source->internal_link_count);
        $this->assertSame(1, $target->incoming_link_count);
        $this->assertFalse($target->is_orphan_page);
    }

    #[Test]
    public function it_skips_nofollow_links_when_follow_nofollow_is_disabled(): void
    {
        $site = Site::create([
            'domain' => 'example.com',
            'name' => 'Example',
        ]);

        SiteCrawlSetting::create([
            'site_id' => $site->id,
            'max_pages' => 100,
            'crawl_delay' => 1,
            'max_depth' => 4,
            'obey_robots' => true,
            'follow_nofollow' => false,
        ]);

        $source = UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/source',
            'normalized_url' => 'https://example.com/source',
            'path' => '/source',
            'status' => 'completed',
            'indexability_status' => 'indexable',
            'page_type' => 'other',
        ]);

        UrlInventory::create([
            'site_id' => $site->id,
            'url' => 'https://example.com/target',
            'normalized_url' => 'https://example.com/target',
            'path' => '/target',
            'status' => 'queued',
            'indexability_status' => 'unknown',
            'page_type' => 'other',
        ]);

        $service = app(CrawlQueueService::class);

        $method = new ReflectionMethod(CrawlQueueService::class, 'storeDiscoveredLinks');
        $method->setAccessible(true);

        $method->invoke($service, $site, $source, [
            ['url' => '/target', 'anchor_text' => 'Nofollow', 'rel' => 'nofollow'],
        ], 1);

        $this->assertSame(0, InternalLink::where('site_id', $site->id)->count());
    }
}
