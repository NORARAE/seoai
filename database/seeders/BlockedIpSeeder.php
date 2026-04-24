<?php

namespace Database\Seeders;

use App\Models\BlockedIp;
use Illuminate\Database\Seeder;

class BlockedIpSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'ip_address' => '80.94.95.202',
                'reason' => 'Repeated inquiry spam — VPN/proxy — submitted fake company name "google" — Budapest, Hungary',
                'source' => 'manual',
            ],
        ];

        foreach ($entries as $entry) {
            BlockedIp::firstOrCreate(
                ['ip_address' => $entry['ip_address']],
                [
                    'reason' => $entry['reason'],
                    'source' => $entry['source'],
                    'blocked_at' => now(),
                ]
            );
        }
    }
}
