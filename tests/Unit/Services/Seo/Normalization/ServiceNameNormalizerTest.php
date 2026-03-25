<?php

namespace Tests\Unit\Services\Seo\Normalization;

use App\Services\Seo\Normalization\ServiceNameNormalizer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServiceNameNormalizerTest extends TestCase
{
    #[Test]
    public function it_returns_mapped_label_for_known_service_slug(): void
    {
        $normalizer = new ServiceNameNormalizer();

        $this->assertSame('Meth Residue Remediation', $normalizer->labelFromSlug('meth-residue-cleanup'));
    }

    #[Test]
    public function it_normalizes_slug_and_strips_invalid_characters(): void
    {
        $normalizer = new ServiceNameNormalizer();

        $this->assertSame('biohazard-cleanup', $normalizer->normalizeSlug('  Biohazard_Cleanup!! '));
    }

    #[Test]
    public function it_builds_base_name_without_suffix(): void
    {
        $normalizer = new ServiceNameNormalizer();

        $this->assertSame('Trauma Scene', $normalizer->baseFromSlug('trauma-scene-cleanup'));
    }

    #[Test]
    public function it_applies_suffix_fallback_heuristics_for_unknown_slug(): void
    {
        $normalizer = new ServiceNameNormalizer();

        $this->assertSame('Property Biohazard Testing', $normalizer->suffixLabelFromSlug('property-biohazard-testing'));
    }
}
