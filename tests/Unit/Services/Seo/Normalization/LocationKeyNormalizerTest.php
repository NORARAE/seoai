<?php

namespace Tests\Unit\Services\Seo\Normalization;

use App\Services\Seo\Normalization\LocationKeyNormalizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocationKeyNormalizerTest extends TestCase
{
    #[Test]
    public function it_normalizes_city_and_county_slugs(): void
    {
        $normalizer = new LocationKeyNormalizer();

        $this->assertSame('federal-way', $normalizer->normalizeCitySlug('Federal Way'));
        $this->assertSame('king', $normalizer->normalizeCountySlug('King County'));
    }

    #[Test]
    public function it_normalizes_state_name_and_abbreviation_to_two_letter_code(): void
    {
        $normalizer = new LocationKeyNormalizer();

        $this->assertSame('wa', $normalizer->normalizeStateCode('Washington'));
        $this->assertSame('or', $normalizer->normalizeStateCode('OR'));
    }

    #[Test]
    public function it_builds_parent_city_key_from_city_and_state(): void
    {
        $normalizer = new LocationKeyNormalizer();

        $this->assertSame('seattle-wa', $normalizer->buildParentCityKey('Seattle', 'WA'));
    }
}
