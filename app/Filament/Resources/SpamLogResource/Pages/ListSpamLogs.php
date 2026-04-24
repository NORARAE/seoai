<?php

namespace App\Filament\Resources\SpamLogResource\Pages;

use App\Filament\Resources\SpamLogResource;
use App\Filament\Widgets\SpamIntelligenceWidget;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSpamLogs extends ListRecords
{
    protected static string $resource = SpamLogResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => \App\Models\SpamLog::count()),

            'blocked' => Tab::make('Blocked')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('action', 'block'))
                ->badge(fn () => \App\Models\SpamLog::where('action', 'block')->count())
                ->badgeColor('danger'),

            'flagged' => Tab::make('Flagged')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('action', 'flag'))
                ->badge(fn () => \App\Models\SpamLog::where('action', 'flag')->count())
                ->badgeColor('warning'),

            'unreviewed' => Tab::make('Unreviewed')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_reviewed', false))
                ->badge(fn () => \App\Models\SpamLog::where('is_reviewed', false)->count())
                ->badgeColor('gray'),

            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $q) => $q->whereDate('created_at', today()))
                ->badge(fn () => \App\Models\SpamLog::whereDate('created_at', today())->count())
                ->badgeColor('info'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SpamIntelligenceWidget::class,
        ];
    }
}
