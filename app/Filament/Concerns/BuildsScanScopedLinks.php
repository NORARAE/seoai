<?php

namespace App\Filament\Concerns;

use App\Support\ScanContext;

trait BuildsScanScopedLinks
{
    protected function scanScopedTableFilters(ScanContext $context): array
    {
        return $this->explicitScanScopedTableFilters($context->siteId(), $context->scanRunId());
    }

    protected function explicitScanScopedTableFilters(?int $siteId, ?int $scanRunId, array $additionalFilters = []): array
    {
        if (! $siteId || ! $scanRunId) {
            return $additionalFilters;
        }

        return array_merge($additionalFilters, [
            'current_scan' => [
                'isActive' => true,
                'site_id' => $siteId,
                'scan_run_id' => $scanRunId,
            ],
        ]);
    }

    protected function scanScopedUrl(string $resourceClass, ScanContext $context, string $page = 'index'): string
    {
        return $this->explicitScanScopedUrl($resourceClass, $context->siteId(), $context->scanRunId(), $page);
    }

    protected function explicitScanScopedUrl(string $resourceClass, ?int $siteId, ?int $scanRunId, string $page = 'index', array $additionalFilters = []): string
    {
        $params = [];

        if ($filters = $this->explicitScanScopedTableFilters($siteId, $scanRunId, $additionalFilters)) {
            $params['tableFilters'] = $filters;
        }

        return $resourceClass::getUrl($page, $params);
    }
}