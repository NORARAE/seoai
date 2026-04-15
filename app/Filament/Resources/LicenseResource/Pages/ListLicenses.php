<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLicenses extends ListRecords
{
    protected static string $resource = LicenseResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => \App\Models\License::count()),

            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge(fn () => \App\Models\License::where('status', 'active')->count())
                ->badgeColor('success'),

            'trial' => Tab::make('Trial')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'trial'))
                ->badge(fn () => \App\Models\License::where('status', 'trial')->count())
                ->badgeColor('info'),

            'crypto' => Tab::make('Crypto Paid')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_method', 'crypto'))
                ->badge(fn () => \App\Models\License::where('payment_method', 'crypto')->count())
                ->badgeColor('warning'),

            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'expired'))
                ->badge(fn () => \App\Models\License::where('status', 'expired')->count())
                ->badgeColor('danger'),
        ];
    }
}
