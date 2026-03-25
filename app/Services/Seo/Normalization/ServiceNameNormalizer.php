<?php

namespace App\Services\Seo\Normalization;

class ServiceNameNormalizer
{
    /**
     * Canonical label map ported from legacy WordPress rules.
     *
     * @var array<string, string>
     */
    protected array $labelMap = [
        'biohazard-cleanup' => 'Biohazard Cleanup',
        'crime-scene-cleanup' => 'Crime Scene Cleanup',
        'unattended-death-cleanup' => 'Unattended Death Cleanup',
        'suicide-cleanup' => 'Suicide Cleanup',
        'hoarding-cleanup' => 'Hoarding Cleanup',
        'hazardous-waste-cleanup' => 'Hazardous Waste Cleanup',
        'drug-cleanup' => 'Drug Cleanup',
        'decomposition-cleanup' => 'Decomposition Cleanup',
        'trauma-scene-cleanup' => 'Trauma Scene Cleanup',
        'odor-removal' => 'Odor Removal',
        'vehicle-decontamination' => 'Vehicle Decontamination',
        'estate-cleanout' => 'Estate Cleanout',
        'meth-testing' => 'Meth Testing',
        'drug-testing' => 'Drug Testing',
        'meth-residue-remediation' => 'Meth Residue Remediation',
        'meth-residue-cleanup' => 'Meth Residue Remediation',
        'meth-remediation' => 'Meth Remediation',
        'meth-decontamination' => 'Meth Decontamination',
        'meth-contaminated-house-cleanup' => 'Meth Decontamination',
        'disinfecting-services' => 'Disinfection Services',
        'biohazard-remediation' => 'Biohazard Remediation',
        'black-mold-removal' => 'Black Mold Removal',
        'mold-remediation' => 'Mold Remediation',
        'attic-mold-removal' => 'Attic Mold Removal',
        'basement-mold-removal' => 'Basement Mold Removal',
        'bathroom-mold-remediation' => 'Bathroom Mold Remediation',
    ];

    public function normalizeSlug(string $serviceSlug): string
    {
        $normalized = strtolower(trim($serviceSlug));
        $normalized = str_replace(['_', ' '], '-', $normalized);
        $normalized = preg_replace('/[^a-z0-9-]/', '', $normalized) ?? $normalized;
        $normalized = preg_replace('/-+/', '-', $normalized) ?? $normalized;

        return trim($normalized, '-');
    }

    public function labelFromSlug(string $serviceSlug): string
    {
        $normalized = $this->normalizeSlug($serviceSlug);

        return $this->labelMap[$normalized] ?? $this->titleFromSlug($normalized);
    }

    public function baseFromSlug(string $serviceSlug): string
    {
        $normalized = $this->normalizeSlug($serviceSlug);
        $base = preg_replace('/-(cleanup|cleaning|services|service|removal|remediation|decontamination|testing)$/', '', $normalized) ?? $normalized;

        return $this->titleFromSlug($base);
    }

    public function suffixLabelFromSlug(string $serviceSlug): string
    {
        $normalized = $this->normalizeSlug($serviceSlug);

        if (!empty($this->labelMap[$normalized])) {
            return $this->labelMap[$normalized];
        }

        $base = $this->baseFromSlug($normalized);

        if (str_contains($normalized, 'testing')) {
            return "{$base} Testing";
        }

        if (str_contains($normalized, 'decontamination')) {
            return "{$base} Decontamination";
        }

        if (str_contains($normalized, 'remediation')) {
            return "{$base} Remediation";
        }

        if (str_contains($normalized, 'cleaning')) {
            return "{$base} Cleaning";
        }

        if (str_contains($normalized, 'removal')) {
            return "{$base} Removal";
        }

        if (str_contains($normalized, 'services')) {
            return "{$base} Services";
        }

        return "{$base} Services";
    }

    protected function titleFromSlug(string $slug): string
    {
        return ucwords(str_replace('-', ' ', $slug));
    }
}
