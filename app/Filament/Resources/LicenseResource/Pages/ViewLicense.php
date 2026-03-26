<?php

namespace App\Filament\Resources\LicenseResource\Pages;

use App\Filament\Resources\LicenseResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewLicense extends ViewRecord
{
    protected static string $resource = LicenseResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('License Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('license_key')->label('License Key')->copyable()->fontFamily('mono'),
                    TextEntry::make('status')->label('Status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'   => 'success',
                            'trial'    => 'info',
                            'expired'  => 'danger',
                            default    => 'gray',
                        }),
                    TextEntry::make('plan')->label('Plan')->badge(),
                    TextEntry::make('urls_allowed')->label('URLs Allowed')
                        ->formatStateUsing(fn ($state) => number_format($state)),
                    TextEntry::make('expires_at')->label('Expires')->dateTime('M j, Y')->placeholder('No expiry'),
                    TextEntry::make('trial_ends_at')->label('Trial Ends')->dateTime('M j, Y')->placeholder('—'),
                ]),

            Section::make('Customer')
                ->columns(2)
                ->schema([
                    TextEntry::make('customer_name')->label('Name')->placeholder('—'),
                    TextEntry::make('customer_email')->label('Email'),
                    TextEntry::make('site_url')->label('Site URL')
                        ->url(fn ($state) => $state)->openUrlInNewTab(),
                ]),

            Section::make('Payment')
                ->columns(2)
                ->schema([
                    TextEntry::make('payment_method')->label('Method')
                        ->badge()
                        ->color(fn ($state): string => match ($state ?? '') {
                            'crypto' => 'warning',
                            'stripe' => 'info',
                            default  => 'gray',
                        })
                        ->placeholder('stripe'),

                    TextEntry::make('stripe_subscription_id')
                        ->label('Stripe Subscription ID')
                        ->copyable()
                        ->fontFamily('mono')
                        ->placeholder('—')
                        ->visible(fn ($record) => $record->payment_method !== 'crypto'),

                    TextEntry::make('stripe_customer_id')
                        ->label('Stripe Customer ID')
                        ->copyable()
                        ->fontFamily('mono')
                        ->placeholder('—')
                        ->visible(fn ($record) => $record->payment_method !== 'crypto'),

                    TextEntry::make('crypto_charge_id')
                        ->label('Coinbase Charge ID')
                        ->copyable()
                        ->fontFamily('mono')
                        ->placeholder('—')
                        ->visible(fn ($record) => $record->payment_method === 'crypto'),
                ]),

        ]);
    }
}
