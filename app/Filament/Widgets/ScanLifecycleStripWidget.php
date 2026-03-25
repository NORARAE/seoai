<?php

namespace App\Filament\Widgets;

use App\Support\CurrentScanResolver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ScanLifecycleStripWidget extends Widget
{
    protected string $view = 'filament.widgets.scan-lifecycle-strip-widget';

    protected int | string | array $columnSpan = 12;

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $context = CurrentScanResolver::dtoForUser(Auth::user());

        return [
            'context' => $context,
            'stages' => [
                [
                    'key' => 'selected',
                    'label' => 'Choose Site',
                    'count' => $context->site ? 1 : 0,
                    'state' => $context->site ? 'complete' : 'pending',
                    'description' => 'Pick the site you want to work on.',
                ],
                [
                    'key' => 'started',
                    'label' => 'Scan Your Site',
                    'count' => $context->scanRunId() || $context->activeScanRunId() ? 1 : 0,
                    'state' => $context->activeScanRunId() ? 'active' : (($context->scanRunId() || $context->site) ? 'complete' : 'pending'),
                    'description' => 'Start a scan so the platform can map the site.',
                ],
                [
                    'key' => 'discovered',
                    'label' => 'Discover Pages',
                    'count' => $context->discovered,
                    'state' => $context->discovered > 0 ? 'complete' : 'pending',
                    'description' => 'Find the pages that belong to this site.',
                ],
                [
                    'key' => 'crawled',
                    'label' => 'Analyze Pages',
                    'count' => $context->crawled,
                    'state' => $context->state === 'scanning' ? 'active' : ($context->crawled > 0 ? 'complete' : 'pending'),
                    'description' => 'Read titles, headings, content, and other page signals.',
                ],
                [
                    'key' => 'opportunities',
                    'label' => 'Review Opportunities',
                    'count' => $context->opportunities,
                    'state' => $context->opportunities > 0 ? 'complete' : 'pending',
                    'description' => 'See what to create, improve, or fix next.',
                ],
            ],
        ];
    }
}