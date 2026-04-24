<?php

namespace App\Filament\Resources\SpamLogResource\Pages;

use App\Filament\Resources\SpamLogResource;
use App\Models\SpamLog;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewSpamLog extends ViewRecord
{
    protected static string $resource = SpamLogResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Decision')
                ->columns(4)
                ->schema([
                    TextEntry::make('action')
                        ->label('Decision')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'block' => 'danger',
                            'flag'  => 'warning',
                            default => 'gray',
                        }),

                    TextEntry::make('reason')
                        ->label('Primary Reason')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'antispam_blocked', 'high_risk_score', 'honeypot_triggered' => 'danger',
                            'duplicate_submission', 'antispam_flagged', 'medium_risk_allowed' => 'warning',
                            default => 'gray',
                        }),

                    TextEntry::make('risk_score')
                        ->label('Risk Score')
                        ->numeric(1),

                    TextEntry::make('spam_risk')
                        ->label('Spam Risk')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'high'   => 'danger',
                            'medium' => 'warning',
                            default  => 'success',
                        }),
                ]),

            Section::make('Submitter')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Name')->placeholder('—'),
                    TextEntry::make('email')->label('Email')->copyable()->placeholder('—'),
                    TextEntry::make('company')->label('Company')->placeholder('—'),
                    TextEntry::make('ip_address')->label('IP Address')->copyable()->fontFamily('mono')->placeholder('—'),
                    TextEntry::make('user_agent')->label('User Agent')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Turnstile')
                ->columns(2)
                ->schema([
                    TextEntry::make('turnstile_valid')
                        ->label('Valid')
                        ->formatStateUsing(fn (?bool $state, SpamLog $record): string => match (true) {
                            $state === true  => 'Passed',
                            $state === false => 'Failed',
                            $record->turnstile_reason === 'turnstile_missing' => 'Missing',
                            default          => 'N/A',
                        })
                        ->badge()
                        ->color(fn (?bool $state): string => match ($state) {
                            true  => 'success',
                            false => 'danger',
                            default => 'gray',
                        }),

                    TextEntry::make('turnstile_reason')
                        ->label('Reason Code')
                        ->placeholder('—'),
                ]),

            Section::make('Network')
                ->columns(2)
                ->schema([
                    TextEntry::make('inquiry.ip_country')->label('Country')->placeholder('—'),
                    TextEntry::make('inquiry.ip_region')->label('Region')->placeholder('—'),
                    TextEntry::make('inquiry.ip_city')->label('City')->placeholder('—'),
                    TextEntry::make('inquiry.ip_isp')->label('ISP')->placeholder('—'),
                    IconEntry::make('inquiry.ip_is_proxy')->label('VPN / Proxy')->boolean(),
                    IconEntry::make('inquiry.ip_is_hosting')->label('Hosting / DC IP')->boolean(),
                ]),

            Section::make('Spam Signals')
                ->schema([
                    TextEntry::make('signals')
                        ->label('')
                        ->formatStateUsing(function ($state): string {
                            if (empty($state)) {
                                return 'No signals recorded.';
                            }
                            $lines = array_map(
                                fn (string $s): string => '• ' . str_replace('_', ' ', $s),
                                (array) $state
                            );
                            return implode("\n", $lines);
                        })
                        ->prose()
                        ->columnSpanFull(),
                ]),

            Section::make('Linked Inquiry')
                ->columns(2)
                ->schema([
                    TextEntry::make('inquiry.tier')->label('Tier')->badge()->placeholder('—'),
                    TextEntry::make('inquiry.type')->label('Type')->placeholder('—'),
                    TextEntry::make('inquiry.website')
                        ->label('Website')
                        ->url(fn ($state) => $state)
                        ->openUrlInNewTab()
                        ->placeholder('—'),
                    TextEntry::make('inquiry.message')
                        ->label('Message')
                        ->prose()
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Admin')
                ->columns(2)
                ->schema([
                    IconEntry::make('is_reviewed')->label('Reviewed')->boolean(),
                    TextEntry::make('created_at')
                        ->label('Logged At')
                        ->dateTime('M j, Y g:i:s A')
                        ->timezone('America/Los_Angeles'),
                ]),
        ]);
    }
}
