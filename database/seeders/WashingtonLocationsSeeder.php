<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\County;
use App\Models\State;
use Illuminate\Database\Seeder;

class WashingtonLocationsSeeder extends Seeder
{
    /**
     * Seed Washington State locations for testing
     */
    public function run(): void
    {
        $this->command->info('🌲 Seeding Washington State locations...');

        // Create or get Washington state
        $washington = State::firstOrCreate(
            ['code' => 'WA'],
            [
                'name' => 'Washington',
                'slug' => 'washington',
            ]
        );

        $this->command->info("✅ State: {$washington->name}");

        // Define test counties
        $counties = [
            ['name' => 'King County', 'slug' => 'king'],
            ['name' => 'Pierce County', 'slug' => 'pierce'],
            ['name' => 'Snohomish County', 'slug' => 'snohomish'],
            ['name' => 'Spokane County', 'slug' => 'spokane'],
            ['name' => 'Kitsap County', 'slug' => 'kitsap'],
            ['name' => 'Thurston County', 'slug' => 'thurston'],
        ];

        $countyModels = [];
        foreach ($counties as $countyData) {
            $county = County::firstOrCreate(
                [
                    'state_id' => $washington->id,
                    'slug' => $countyData['slug'],
                ],
                [
                    'name' => $countyData['name'],
                ]
            );
            $countyModels[$countyData['slug']] = $county;
        }

        $this->command->info("✅ Counties: " . count($countyModels));

        // Define test cities with realistic data
        $cities = [
            // King County
            [
                'name' => 'Seattle',
                'slug' => 'seattle',
                'county' => 'king',
                'latitude' => 47.6062,
                'longitude' => -122.3321,
                'population' => 737015,
                'is_county_seat' => true,
                'is_priority' => true,
            ],
            [
                'name' => 'Bellevue',
                'slug' => 'bellevue',
                'county' => 'king',
                'latitude' => 47.6101,
                'longitude' => -122.2015,
                'population' => 148164,
                'is_county_seat' => false,
                'is_priority' => true,
            ],
            [
                'name' => 'Kent',
                'slug' => 'kent',
                'county' => 'king',
                'latitude' => 47.3809,
                'longitude' => -122.2348,
                'population' => 136588,
                'is_priority' => false,
            ],
            [
                'name' => 'Renton',
                'slug' => 'renton',
                'county' => 'king',
                'latitude' => 47.4829,
                'longitude' => -122.2171,
                'population' => 106785,
                'is_priority' => false,
            ],
            [
                'name' => 'Federal Way',
                'slug' => 'federal-way',
                'county' => 'king',
                'latitude' => 47.3223,
                'longitude' => -122.3126,
                'population' => 101030,
                'is_priority' => false,
            ],

            // Pierce County
            [
                'name' => 'Tacoma',
                'slug' => 'tacoma',
                'county' => 'pierce',
                'latitude' => 47.2529,
                'longitude' => -122.4443,
                'population' => 219346,
                'is_county_seat' => true,
                'is_priority' => true,
            ],
            [
                'name' => 'Lakewood',
                'slug' => 'lakewood',
                'county' => 'pierce',
                'latitude' => 47.1718,
                'longitude' => -122.5185,
                'population' => 63612,
                'is_priority' => false,
            ],
            [
                'name' => 'Puyallup',
                'slug' => 'puyallup',
                'county' => 'pierce',
                'latitude' => 47.1854,
                'longitude' => -122.2929,
                'population' => 42973,
                'is_priority' => false,
            ],

            // Snohomish County
            [
                'name' => 'Everett',
                'slug' => 'everett',
                'county' => 'snohomish',
                'latitude' => 47.9790,
                'longitude' => -122.2021,
                'population' => 110629,
                'is_county_seat' => true,
                'is_priority' => true,
            ],
            [
                'name' => 'Marysville',
                'slug' => 'marysville',
                'county' => 'snohomish',
                'latitude' => 48.0518,
                'longitude' => -122.1771,
                'population' => 70714,
                'is_priority' => false,
            ],
            [
                'name' => 'Lynnwood',
                'slug' => 'lynnwood',
                'county' => 'snohomish',
                'latitude' => 47.8210,
                'longitude' => -122.3151,
                'population' => 38568,
                'is_priority' => false,
            ],

            // Spokane County
            [
                'name' => 'Spokane',
                'slug' => 'spokane',
                'county' => 'spokane',
                'latitude' => 47.6588,
                'longitude' => -117.4260,
                'population' => 228989,
                'is_county_seat' => true,
                'is_priority' => true,
            ],
            [
                'name' => 'Spokane Valley',
                'slug' => 'spokane-valley',
                'county' => 'spokane',
                'latitude' => 47.6732,
                'longitude' => -117.2394,
                'population' => 102976,
                'is_priority' => false,
            ],

            // Kitsap County
            [
                'name' => 'Bremerton',
                'slug' => 'bremerton',
                'county' => 'kitsap',
                'latitude' => 47.5673,
                'longitude' => -122.6326,
                'population' => 43505,
                'is_county_seat' => true,
                'is_priority' => true,
            ],
            [
                'name' => 'Port Orchard',
                'slug' => 'port-orchard',
                'county' => 'kitsap',
                'latitude' => 47.5403,
                'longitude' => -122.6365,
                'population' => 15587,
                'is_priority' => false,
            ],

            // Thurston County
            [
                'name' => 'Olympia',
                'slug' => 'olympia',
                'county' => 'thurston',
                'latitude' => 47.0379,
                'longitude' => -122.9007,
                'population' => 55605,
                'is_county_seat' => true,
                'is_priority' => true,
            ],
            [
                'name' => 'Lacey',
                'slug' => 'lacey',
                'county' => 'thurston',
                'latitude' => 47.0343,
                'longitude' => -122.8232,
                'population' => 53526,
                'is_priority' => false,
            ],
            [
                'name' => 'Tumwater',
                'slug' => 'tumwater',
                'county' => 'thurston',
                'latitude' => 47.0073,
                'longitude' => -122.9093,
                'population' => 25350,
                'is_priority' => false,
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($cities as $cityData) {
            $county = $countyModels[$cityData['county']] ?? null;

            if (!$county) {
                $this->command->warn("  ⚠️  Skipping {$cityData['name']}: county not found");
                $skipped++;
                continue;
            }

            $city = City::firstOrCreate(
                [
                    'state_id' => $washington->id,
                    'slug' => $cityData['slug'],
                ],
                [
                    'name' => $cityData['name'],
                    'county_id' => $county->id,
                    'latitude' => $cityData['latitude'],
                    'longitude' => $cityData['longitude'],
                    'population' => $cityData['population'],
                    'is_county_seat' => $cityData['is_county_seat'] ?? false,
                    'is_priority' => $cityData['is_priority'] ?? false,
                ]
            );

            if ($city->wasRecentlyCreated) {
                $created++;
                $this->command->info("  ✅ Created: {$city->name}");
            } else {
                $skipped++;
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Created {$created} new cities");
        $this->command->info("   Skipped {$skipped} existing cities");
        $this->command->info("   Total Washington cities: " . City::where('state_id', $washington->id)->count());
    }
}
