<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\FrontendDevRestricted;

use App\Filament\Resources\LicenseResource\Pages\ListLicenses;
use App\Filament\Resources\LicenseResource\Pages\ViewLicense;
use App\Models\License;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicenseResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = License::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Licenses';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('site_url')
                    ->label('Site')
                    ->searchable()
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->limit(40),

                TextColumn::make('plan')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'agency_10k' => 'success',
                        'agency_5k'  => 'info',
                        default      => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'trial'    => 'info',
                        'expired'  => 'danger',
                        'inactive' => 'gray',
                        default    => 'gray',
                    }),

                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->sortable()
                    ->color(fn ($state): string => match ($state ?? '') {
                        'crypto' => 'warning',
                        'stripe' => 'info',
                        default  => 'gray',
                    })
                    ->placeholder('stripe'),

                TextColumn::make('crypto_charge_id')
                    ->label('Crypto Charge ID')
                    ->limit(20)
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->timezone('America/Los_Angeles'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active'   => 'Active',
                        'trial'    => 'Trial',
                        'expired'  => 'Expired',
                        'inactive' => 'Inactive',
                    ]),

                SelectFilter::make('plan')
                    ->options(fn () => \App\Models\License::select('plan')->distinct()->pluck('plan', 'plan')->toArray()),

                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'crypto' => 'Crypto',
                        'stripe' => 'Stripe / Card',
                    ]),

                Filter::make('crypto_only')
                    ->label('Crypto Paid Only')
                    ->query(fn (Builder $query): Builder => $query->where('payment_method', 'crypto')),

                Filter::make('active_only')
                    ->label('Active Only')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active')),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLicenses::route('/'),
            'view'  => ViewLicense::route('/{record}'),
        ];
    }
}
