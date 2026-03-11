<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user (or update if exists)
        User::updateOrCreate(
            ['email' => 'nora@seoaico.com'],
            [
                'name' => 'Nora Genetti',
                'password' => \Hash::make('password'),
            ]
        );

        // Seed location data (states, counties, cities, services)
        $this->call(LocationSeeder::class);
    }
}
