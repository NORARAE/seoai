<?php

namespace Tests\Unit\Services\Discovery;

use App\Services\Discovery\UrlNormalizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UrlNormalizerTest extends TestCase
{
    #[Test]
    public function it_normalizes_url_and_strips_tracking_params(): void
    {
        $normalizer = new UrlNormalizer();

        $result = $normalizer->normalize('HTTPS://Example.com/Services/?utm_source=google&b=2&a=1#section');

        $this->assertSame('https://example.com/Services?a=1&b=2', $result['normalized_url']);
        $this->assertSame('/Services', $result['path']);
    }

    #[Test]
    public function it_detects_internal_urls_ignoring_www_prefix(): void
    {
        $normalizer = new UrlNormalizer();

        $this->assertTrue($normalizer->isInternal('https://www.example.com/page', 'example.com'));
        $this->assertFalse($normalizer->isInternal('https://other.com/page', 'example.com'));
    }

    #[Test]
    public function it_resolves_relative_urls_against_base(): void
    {
        $normalizer = new UrlNormalizer();

        $resolved = $normalizer->resolveUrl('pricing', 'https://example.com/services/water-damage');

        $this->assertSame('https://example.com/services/pricing', $resolved);
    }
}
