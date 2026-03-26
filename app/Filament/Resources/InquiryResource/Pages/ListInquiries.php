<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => \App\Models\Inquiry::count()),

            'new' => Tab::make('New')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'new'))
                ->badge(fn () => \App\Models\Inquiry::where('status', 'new')->count())
                ->badgeColor('info'),

            'high_risk' => Tab::make('High Risk')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('spam_risk', 'high'))
                ->badge(fn () => \App\Models\Inquiry::where('spam_risk', 'high')->count())
                ->badgeColor('danger'),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(fn () => \App\Models\Inquiry::where('status', 'rejected')->count())
                ->badgeColor('danger'),

            'converted' => Tab::make('Converted')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'converted'))
                ->badge(fn () => \App\Models\Inquiry::where('status', 'converted')->count())
                ->badgeColor('success'),
        ];
    }
}
