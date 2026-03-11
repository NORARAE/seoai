<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\County;
use App\Models\Service;
use App\Models\State;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Washington State
        $washington = State::create([
            'name' => 'Washington',
            'code' => 'WA',
            'slug' => 'washington',
        ]);

        // Create Counties
        $kingCounty = County::create([
            'state_id' => $washington->id,
            'name' => 'King County',
            'slug' => 'king',
        ]);

        $pierceCounty = County::create([
            'state_id' => $washington->id,
            'name' => 'Pierce County',
            'slug' => 'pierce',
        ]);

        $snohomishCounty = County::create([
            'state_id' => $washington->id,
            'name' => 'Snohomish County',
            'slug' => 'snohomish',
        ]);

        // Create Cities
        $cities = [
            [
                'state_id' => $washington->id,
                'county_id' => $kingCounty->id,
                'name' => 'Seattle',
                'slug' => 'seattle',
                'latitude' => 47.6062,
                'longitude' => -122.3321,
                'population' => 749256,
                'is_county_seat' => true,
                'is_priority' => true,
            ],
            [
                'state_id' => $washington->id,
                'county_id' => $kingCounty->id,
                'name' => 'Bellevue',
                'slug' => 'bellevue',
                'latitude' => 47.6101,
                'longitude' => -122.2015,
                'population' => 151854,
                'is_county_seat' => false,
                'is_priority' => true,
            ],
            [
                'state_id' => $washington->id,
                'county_id' => $pierceCounty->id,
                'name' => 'Tacoma',
                'slug' => 'tacoma',
                'latitude' => 47.2529,
                'is_county_seat' => true,
                'is_priority' => true,
                'longitude' => -122.4443,
                'population' => 219346,
            ],
            [
                'state_id' => $washington->id,
                'county_id' => $snohomishCounty->id,
                'name' => 'Everett',
                'slug' => 'everett',
                'latitude' => 47.9790,
                'is_county_seat' => true,
                'is_priority' => true,
                'longitude' => -122.2021,
                'population' => 110629,
            ],
            [
                'state_id' => $washington->id,
                'county_id' => $kingCounty->id,
                'name' => 'Renton',
                'is_county_seat' => false,
                'is_priority' => false,
                'slug' => 'renton',
                'latitude' => 47.4829,
                'longitude' => -122.2171,
                'population' => 106785,
            ],
            [
                'state_id' => $washington->id,
                'county_id' => $kingCounty->id,
                'name' => 'Kent',
                'is_county_seat' => false,
                'is_priority' => false,
                'slug' => 'kent',
                'latitude' => 47.3809,
                'longitude' => -122.2348,
                'population' => 136588,
            ],
            [
                'state_id' => $washington->id,
                'is_county_seat' => false,
                'is_priority' => false,
                'county_id' => $kingCounty->id,
                'name' => 'Redmond',
                'slug' => 'redmond',
                'latitude' => 47.6740,
                'longitude' => -122.1215,
                'population' => 73256,
            ],
            [
                'state_id' => $washington->id,
                'is_county_seat' => false,
                'is_priority' => false,
                'county_id' => $kingCounty->id,
                'name' => 'Shoreline',
                'slug' => 'shoreline',
                'latitude' => 47.7557,
                'longitude' => -122.3415,
                'population' => 58109,
            ],
        ];

        foreach ($cities as $cityData) {
            City::create($cityData);
        }

        // Create Services
        $services = [
            [
                'name' => 'Biohazard Cleanup',
                'slug' => 'biohazard-cleanup',
                'is_active' => true,
            ],
            [
                'name' => 'Crime Scene Cleanup',
                'slug' => 'crime-scene-cleanup',
                'is_active' => true,
            ],
            [
                'name' => 'Unattended Death Cleanup',
                'slug' => 'unattended-death-cleanup',
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }
    }
}
