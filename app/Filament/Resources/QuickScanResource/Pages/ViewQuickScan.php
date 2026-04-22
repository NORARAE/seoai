<?php

namespace App\Filament\Resources\QuickScanResource\Pages;

use App\Filament\Resources\QuickScanResource;
use App\Models\QuickScan;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewQuickScan extends ViewRecord
{
    protected static string $resource = QuickScanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_report')
                ->label('Open Report')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn(QuickScan $record): string => route('report.show', ['scan' => $record->id]))
                ->openUrlInNewTab()
                ->color('success'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Purchase Details')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('id')
                                ->label('Scan ID')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('email')
                                ->label('Buyer Email')
                                ->copyable(),

                            TextEntry::make('domain')
                                ->label('Domain')
                                ->copyable()
                                ->placeholder('—'),

                            TextEntry::make('url')
                                ->label('URL Scanned')
                                ->limit(60)
                                ->tooltip(fn(QuickScan $r) => $r->url ?? '')
                                ->copyable(),

                            TextEntry::make('paid')
                                ->label('Payment Received')
                                ->badge()
                                ->formatStateUsing(fn(bool $state): string => $state ? 'Yes — Paid' : 'Not Paid')
                                ->color(fn(bool $state): string => $state ? 'success' : 'danger'),

                            TextEntry::make('status')
                                ->label('Scan Status')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'scanned' => 'success',
                                    'paid' => 'info',
                                    'pending' => 'gray',
                                    'error' => 'danger',
                                    default => 'gray',
                                }),
                        ]),
                    ]),

                Section::make('Score & Analysis')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('score')
                                ->label('AI Visibility Score')
                                ->badge()
                                ->color(fn(?int $state): string => match (true) {
                                    $state === null => 'gray',
                                    $state >= 70 => 'success',
                                    $state >= 40 => 'warning',
                                    default => 'danger',
                                }),

                            TextEntry::make('score_change')
                                ->label('Score Change')
                                ->badge()
                                ->formatStateUsing(fn(?int $state): string => match (true) {
                                    $state === null => '—',
                                    $state > 0 => "+{$state}",
                                    default => (string) $state,
                                })
                                ->color(fn(?int $state): string => match (true) {
                                    $state === null => 'gray',
                                    $state > 0 => 'success',
                                    $state < 0 => 'danger',
                                    default => 'gray',
                                }),

                            TextEntry::make('page_count')
                                ->label('Pages Crawled')
                                ->placeholder('—'),

                            TextEntry::make('domain_scan_count')
                                ->label('Total Scans (Domain)')
                                ->badge()
                                ->color(fn(?int $state): string => match (true) {
                                    $state === null || $state <= 1 => 'gray',
                                    $state >= 3 => 'warning',
                                    default => 'info',
                                }),

                            TextEntry::make('fastest_fix')
                                ->label('Fastest Fix')
                                ->placeholder('—')
                                ->columnSpan(2),
                        ]),
                    ]),

                Section::make('Billing & Upgrade')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('stripe_session_id')
                                ->label('Stripe Session (Initial)')
                                ->copyable()
                                ->placeholder('—'),

                            TextEntry::make('upgrade_plan')
                                ->label('Upgrade Plan')
                                ->badge()
                                ->placeholder('—')
                                ->formatStateUsing(fn(?string $state): string => match ($state) {
                                    'diagnostic' => 'Signal Expansion',
                                    'fix-strategy' => 'Structural Leverage',
                                    'optimization' => 'System Activation',
                                    default => $state ?? '',
                                })
                                ->color(fn(?string $state): string => match ($state) {
                                    'diagnostic' => 'info',
                                    'fix-strategy' => 'warning',
                                    'optimization' => 'success',
                                    default => 'gray',
                                }),

                            TextEntry::make('upgrade_status')
                                ->label('Upgrade Status')
                                ->badge()
                                ->placeholder('—')
                                ->color(fn(?string $state): string => match ($state) {
                                    'paid', 'active' => 'success',
                                    'pending' => 'warning',
                                    'completed' => 'info',
                                    default => 'gray',
                                }),

                            TextEntry::make('upgrade_stripe_session_id')
                                ->label('Stripe Session (Upgrade)')
                                ->copyable()
                                ->placeholder('—'),
                        ]),
                    ]),

                Section::make('Metadata')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('source')
                                ->label('Source')
                                ->badge()
                                ->placeholder('—'),

                            TextEntry::make('ip_address')
                                ->label('IP Address')
                                ->placeholder('—'),

                            TextEntry::make('is_repeat_scan')
                                ->label('Repeat Scan')
                                ->badge()
                                ->formatStateUsing(fn(bool $state): string => $state ? 'Yes' : 'No')
                                ->color(fn(bool $state): string => $state ? 'info' : 'gray'),

                            TextEntry::make('scanned_at')
                                ->label('Scanned At')
                                ->dateTime('M j, Y g:i A')
                                ->placeholder('—'),

                            TextEntry::make('created_at')
                                ->label('Created')
                                ->dateTime('M j, Y g:i A'),

                            TextEntry::make('public_scan_id')
                                ->label('Public Scan ID')
                                ->copyable()
                                ->placeholder('—'),
                        ]),
                    ]),
            ]);
    }
}
