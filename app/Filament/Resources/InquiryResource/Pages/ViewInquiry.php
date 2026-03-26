<?php

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Filament\Resources\InquiryResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewInquiry extends ViewRecord
{
    protected static string $resource = InquiryResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Contact Info')
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('email')->label('Email')
                        ->description(fn ($record) => $record->email_type ? 'Type: ' . strtoupper($record->email_type) : null),
                    TextEntry::make('company')->label('Company'),
                    TextEntry::make('website')->label('Website')->url(fn ($state) => $state)->openUrlInNewTab(),
                    TextEntry::make('tier')->label('Tier')->badge(),
                    TextEntry::make('type')->label('Type')->formatStateUsing(fn ($record) => $record->typeLabel()),
                    TextEntry::make('niche')->label('Niche')->placeholder('—'),
                    TextEntry::make('status')->label('Status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'new'       => 'info',
                            'contacted' => 'warning',
                            'converted' => 'success',
                            'rejected'  => 'danger',
                            default     => 'gray',
                        }),
                ]),

            Section::make('Message')
                ->schema([
                    TextEntry::make('message')->label('')->prose()->columnSpanFull(),
                ]),

            Section::make('Location & IP')
                ->columns(2)
                ->schema([
                    TextEntry::make('ip_address')->label('IP Address')->placeholder('—'),
                    TextEntry::make('ip_country')->label('Country')->placeholder('—'),
                    TextEntry::make('ip_region')->label('Region / State')->placeholder('—'),
                    TextEntry::make('ip_city')->label('City')->placeholder('—'),
                    TextEntry::make('ip_isp')->label('ISP / Org')->placeholder('—'),
                    IconEntry::make('ip_is_proxy')->label('VPN / Proxy')->boolean(),
                    IconEntry::make('ip_is_hosting')->label('Hosting / DC IP')->boolean(),
                ]),

            Section::make('Website Check')
                ->columns(2)
                ->schema([
                    TextEntry::make('url_status')->label('URL Status')->badge()
                        ->color(fn ($state): string => match ($state ?? '') {
                            'valid'        => 'success',
                            'redirect'     => 'warning',
                            'parked', 'suspicious', 'unresolvable' => 'danger',
                            default        => 'gray',
                        }),
                    IconEntry::make('url_is_https')->label('HTTPS')->boolean(),
                    TextEntry::make('domain_age_days')->label('Domain Age (days)')->placeholder('—'),
                ]),

            Section::make('Security & Spam Signals')
                ->columns(2)
                ->schema([
                    TextEntry::make('spam_risk')->label('Spam Risk')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'high'   => 'danger',
                            'medium' => 'warning',
                            default  => 'success',
                        }),
                    TextEntry::make('recaptcha_score')->label('reCAPTCHA Score')
                        ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 2) . ' / 1.00' : '—')
                        ->color(fn ($state): string => $state === null ? 'gray'
                            : ($state >= 0.7 ? 'success' : ($state >= 0.5 ? 'warning' : 'danger'))),
                    TextEntry::make('time_to_submit_seconds')->label('Time to Submit')
                        ->formatStateUsing(fn ($state) => $state !== null ? $state . 's' : '—')
                        ->color(fn ($state): string => ($state !== null && $state < 4) ? 'danger' : 'success'),
                    IconEntry::make('honeypot_triggered')->label('Honeypot Triggered')->boolean(),
                ]),

            Section::make('Company Enrichment')
                ->columns(2)
                ->schema([
                    TextEntry::make('company_enrichment.name')->label('Legal Name')->placeholder('—'),
                    TextEntry::make('company_enrichment.industry')->label('Industry')->placeholder('—'),
                    TextEntry::make('company_enrichment.employees')->label('Employees')
                        ->formatStateUsing(fn ($state) => $state ? number_format($state) : '—'),
                    TextEntry::make('company_enrichment.founded')->label('Founded')->placeholder('—'),
                    TextEntry::make('company_enrichment.location')->label('HQ Location')->placeholder('—'),
                    TextEntry::make('company_enrichment.linkedin')->label('LinkedIn')
                        ->url(fn ($state) => $state ? 'https://linkedin.com/company/' . $state : null)
                        ->openUrlInNewTab()
                        ->placeholder('—'),
                ])
                ->visible(fn ($record) => ! empty($record->company_enrichment)),

        ]);
    }
}
