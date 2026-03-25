<?php

namespace Tests\Unit\Services\Seo\Urls;

use App\Services\Seo\Normalization\LocationKeyNormalizer;
use App\Services\Seo\Normalization\ServiceNameNormalizer;
use App\Services\Seo\Urls\UrlPatternStrategy;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UrlPatternStrategyTest extends TestCase
{
    protected function makeStrategy(): UrlPatternStrategy
    {
        return new UrlPatternStrategy(new ServiceNameNormalizer(), new LocationKeyNormalizer());
    }

    #[Test]
    public function it_builds_neighborhood_nested_path_only(): void
    {
        $strategy = $this->makeStrategy();

        $paths = $strategy->candidatePaths(
            'biohazard-cleanup',
            'west-seattle-seattle-wa',
            ['type' => 'neighborhood', 'city' => 'Seattle', 'state' => 'WA']
        );

        $this->assertSame(['/biohazard-cleanup-seattle-wa/west-seattle/'], $paths);
    }

    #[Test]
    public function it_prioritizes_nested_then_flat_for_nestable_area(): void
    {
        $strategy = $this->makeStrategy();

        $paths = $strategy->candidatePaths(
            'biohazard-cleanup',
            'north-end-everett-wa',
            ['type' => 'area', 'city' => 'Everett', 'state' => 'WA'],
            ['everett-wa' => ['type' => 'city']]
        );

        $this->assertSame([
            '/biohazard-cleanup-everett-wa/north-end/',
            '/biohazard-cleanup-north-end-everett-wa/',
        ], $paths);
    }

    #[Test]
    public function it_builds_flat_path_for_city_type(): void
    {
        $strategy = $this->makeStrategy();

        $paths = $strategy->candidatePaths(
            'crime-scene-cleanup',
            'tacoma-wa',
            ['type' => 'city', 'city' => 'Tacoma', 'state' => 'WA']
        );

        $this->assertSame(['/crime-scene-cleanup-tacoma-wa/'], $paths);
    }
}
