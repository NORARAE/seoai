<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Concerns\FrontendDevRestricted;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Models\Lead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    use FrontendDevRestricted;

    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Leads';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 4;

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

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->canApproveUsers();
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

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('session_type')
                    ->label('Session')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'paid' => 'success',
                        'free' => 'info',
                        default => 'gray',
                    })
                    ->placeholder('—'),

                TextColumn::make('lifecycle_stage')
                    ->label('Stage')
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => match ($state) {
                        Lead::STAGE_ACTIVE               => 'success',
                        Lead::STAGE_APPROVED             => 'success',
                        Lead::STAGE_ONBOARDING_SUBMITTED => 'info',
                        Lead::STAGE_PAID                 => 'warning',
                        Lead::STAGE_BOOKED               => 'gray',
                        Lead::STAGE_REJECTED             => 'danger',
                        Lead::STAGE_LOST                 => 'danger',
                        default                          => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        Lead::STAGE_ONBOARDING_SUBMITTED => 'Onboarding',
                        default                          => ucwords(str_replace('_', ' ', $state ?? 'new')),
                    }),

                TextColumn::make('onboarding_status')
                    ->label('Onboarding')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'approved'  => 'success',
                        'submitted' => 'info',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('M j, Y')
                    ->sortable(),

                TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->sortable()
                    ->color(fn (?string $state): string => match ($state) {
                        'A'     => 'success',
                        'B'     => 'info',
                        'C'     => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('score')
                    ->label('Score')
                    ->suffix('/100')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('lifecycle_stage')
                    ->label('Pipeline Stage')
                    ->options([
                        Lead::STAGE_NEW                  => 'New',
                        Lead::STAGE_BOOKED               => 'Booked',
                        Lead::STAGE_PAID                 => 'Paid',
                        Lead::STAGE_ONBOARDING_SUBMITTED => 'Onboarding Submitted',
                        Lead::STAGE_APPROVED             => 'Approved',
                        Lead::STAGE_ACTIVE               => 'Active',
                        Lead::STAGE_REJECTED             => 'Rejected',
                        Lead::STAGE_LOST                 => 'Lost',
                    ]),

                SelectFilter::make('onboarding_status')
                    ->options([
                        'pending'   => 'Pending',
                        'submitted' => 'Submitted',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                    ]),

                SelectFilter::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'free' => 'Free',
                    ]),
            ])
            ->recordUrl(fn (Lead $r) => static::getUrl('view', ['record' => $r]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'view'  => ViewLead::route('/{record}'),
        ];
    }
}
