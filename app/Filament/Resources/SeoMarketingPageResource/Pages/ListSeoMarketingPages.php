<?php

namespace App\Filament\Resources\SeoMarketingPageResource\Pages;

use App\Filament\Resources\SeoMarketingPageResource;
use App\Models\SeoMarketingPage;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSeoMarketingPages extends ListRecords
{
    protected static string $resource = SeoMarketingPageResource::class;

    protected function getHeaderWidgets(): array
    {
        return [\App\Filament\Widgets\SeoMarketingPageStats::class];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => SeoMarketingPage::count()),

            'core' => Tab::make('Core')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('cluster', 'core'))
                ->badge(fn () => SeoMarketingPage::where('cluster', 'core')->count())
                ->badgeColor('primary'),

            'agency' => Tab::make('Agency')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('cluster', 'agency'))
                ->badge(fn () => SeoMarketingPage::where('cluster', 'agency')->count())
                ->badgeColor('info'),

            'local' => Tab::make('Local')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('cluster', 'local'))
                ->badge(fn () => SeoMarketingPage::where('cluster', 'local')->count())
                ->badgeColor('success'),

            'strategy' => Tab::make('Strategy')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('cluster', 'strategy'))
                ->badge(fn () => SeoMarketingPage::where('cluster', 'strategy')->count())
                ->badgeColor('warning'),

            'industry' => Tab::make('Industry')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('cluster', 'industry'))
                ->badge(fn () => SeoMarketingPage::where('cluster', 'industry')->count())
                ->badgeColor('danger'),

            'money_pages' => Tab::make('Money Pages')
                ->modifyQueryUsing(fn (Builder $q) => $q->whereNotNull('money_page_rank')->orderBy('money_page_rank'))
                ->badge(fn () => SeoMarketingPage::whereNotNull('money_page_rank')->count())
                ->badgeColor('warning'),
        ];
    }
}
