<?php

namespace App\Services;

class MarketCaptureMode
{
    public function forCoverageScore(int $score): array
    {
        if ($score >= 80) {
            return [
                'mode' => 'monitor',
                'label' => 'Market Capture Mode: Monitor',
                'description' => 'Coverage is strong. Focus on selective high-impact expansion.',
            ];
        }

        if ($score >= 55) {
            return [
                'mode' => 'expand',
                'label' => 'Market Capture Mode: Expand',
                'description' => 'Coverage has meaningful gaps. Prioritize missing high-value pages.',
            ];
        }

        return [
            'mode' => 'capture',
            'label' => 'Market Capture Mode: Capture',
            'description' => 'Coverage is low. Run aggressive capture workflows and queue generation.',
        ];
    }
}
