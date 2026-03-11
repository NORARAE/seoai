<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Service;
use App\Services\LocationIntelligenceService;
use Illuminate\Console\Command;

class LocationsDemo extends Command
{
    protected $signature = 'locations:demo';

    protected $description = 'Demonstrate location intelligence features';

    public function handle(LocationIntelligenceService $locationService): int
    {
        $this->info('=== Location Intelligence Demo ===');
        $this->newLine();

        // Get Seattle
        $seattle = City::where('slug', 'seattle')->first();

        if (!$seattle) {
            $this->error('Seattle not found. Run the LocationSeeder first.');
            return self::FAILURE;
        }

        // 1. Show nearby cities
        $this->info('🌍 Nearby cities to Seattle:');
        $nearbyCities = $locationService->getNearbyCities($seattle, 5);

        foreach ($nearbyCities as $city) {
            $this->line("  • {$city->name} ({$city->distance} miles)");
        }

        $this->newLine();

        // 2. Get services
        $services = Service::active()->get();

        if ($services->isEmpty()) {
            $this->warn('No services found. Run the LocationSeeder first.');
            return self::FAILURE;
        }

        // 3. Show county hub slug
        $service = $services->first();
        $county = $seattle->county;
        $countyHubSlug = $locationService->getCountyHubSlug($service, $county);

        $this->info("📍 County hub slug for {$service->name} in {$county->name}:");
        $this->line("  {$countyHubSlug}");
        $this->newLine();

        // 4. Show city page slugs
        $this->info('📄 City page slug examples:');
        foreach ($services as $service) {
            $citySlug = $locationService->getCityPageSlug($service, $seattle);
            $this->line("  • {$service->name}: {$citySlug}");
        }

        $this->newLine();

        // 5. Show county page slugs
        $this->info('📄 County page slug examples:');
        foreach ($services as $service) {
            $countySlug = $locationService->getCountyPageSlug($service, $county);
            $this->line("  • {$service->name}: {$countySlug}");
        }

        $this->newLine();

        // 6. Show service cluster
        $this->info('🎯 Service cluster for Seattle:');
        $cluster = $locationService->getServiceClusterForCity($seattle, $services);

        foreach ($cluster as $item) {
            $this->line("  • {$item['service']} → {$item['slug']}");
        }

        $this->newLine();
        $this->info('✓ Demo complete!');

        return self::SUCCESS;
    }
}
