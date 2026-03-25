<?php

namespace Tests\Unit\Services\Seo\Urls;

use App\Services\Seo\Normalization\LocationKeyNormalizer;
use App\Services\Seo\Normalization\ServiceNameNormalizer;
use App\Services\Seo\Urls\CandidatePathBuilder;
use App\Services\Seo\Urls\UrlPatternStrategy;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CandidatePathBuilderTest extends TestCase
{
    protected function makeBuilder(): CandidatePathBuilder
    {
        $serviceNormalizer = new ServiceNameNormalizer();
        $locationNormalizer = new LocationKeyNormalizer();
        $strategy = new UrlPatternStrategy($serviceNormalizer, $locationNormalizer);

        return new CandidatePathBuilder($strategy, $serviceNormalizer, $locationNormalizer);
    }

    #[Test]
    public function it_builds_service_city_path_from_tokenized_pattern(): void
    {
        $builder = $this->makeBuilder();

        $path = $builder->buildServiceCityPath('Biohazard Cleanup', 'Federal Way', 'WA');

        $this->assertSame('/biohazard-cleanup-federal-way-wa/', $path);
    }

    #[Test]
    public function it_delegates_and_returns_prioritized_candidate_paths(): void
    {
        $builder = $this->makeBuilder();

        $paths = $builder->buildPrioritized(
            'biohazard-cleanup',
            'north-end-everett-wa',
            ['type' => 'area', 'city' => 'Everett', 'state' => 'WA'],
            ['everett-wa' => ['type' => 'town']]
        );

        $this->assertSame('/biohazard-cleanup-everett-wa/north-end/', $paths[0]);
        $this->assertSame('/biohazard-cleanup-north-end-everett-wa/', $paths[1]);
    }
}
