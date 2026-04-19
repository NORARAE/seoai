<?php

namespace Database\Seeders;

use App\Models\BookingAvailability;
use App\Models\ConsultType;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // ── PRIMARY FUNNEL ENTRIES (bookable) ───────────────────────────
            ['name' => 'Market Opportunity Snapshot', 'slug' => 'discovery', 'description' => 'See where you\'re missing visibility — and where your competitors are winning. A focused scan using live search data.', 'duration_minutes' => 20, 'price' => null, 'is_free' => true, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'AI Visibility Consultation', 'slug' => 'audit', 'description' => 'We interpret your AI visibility, qualify the opportunity, and map your expansion path before full system engagement.', 'duration_minutes' => 60, 'price' => 500.00, 'is_free' => false, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Market Growth Plan', 'slug' => 'agency-review', 'description' => 'Legacy advisory option retained for compatibility.', 'duration_minutes' => 60, 'price' => 250.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 3],

            // ── PRIMARY FULL-SERVICE ACTIVATION PATH ────────────────────────
            ['name' => 'Legacy Consultation Alias', 'slug' => 'strategy-session', 'description' => 'Legacy consultation alias retained for compatibility.', 'duration_minutes' => 60, 'price' => 500.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 4],
            ['name' => 'Full System Activation', 'slug' => 'market-expansion', 'description' => 'We build and deploy your visibility infrastructure with ownership of the outcome.', 'duration_minutes' => 60, 'price' => 5000.00, 'is_free' => false, 'is_active' => true, 'sort_order' => 5],

            // ── SECONDARY / LEGACY TYPES (retain for direct-link access) ────
            ['name' => 'Legacy Consultation', 'slug' => 'strategy', 'description' => 'Legacy consultation alias retained for compatibility.', 'duration_minutes' => 60, 'price' => 250.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 10],
            ['name' => 'Full SEO Audit Session', 'slug' => 'seo-audit', 'description' => 'Comprehensive audit of your site\'s SEO health and opportunities.', 'duration_minutes' => 90, 'price' => 400.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 12],
            ['name' => 'Custom Project Scoping', 'slug' => 'project-scoping', 'description' => 'Scope out a custom build or integration project.', 'duration_minutes' => 60, 'price' => 250.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 13],
            ['name' => 'Graphic Design Consult', 'slug' => 'design', 'description' => 'Discuss branding, layout, and visual direction for your project.', 'duration_minutes' => 45, 'price' => 200.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 14],
            ['name' => 'Art & Creative Dev', 'slug' => 'creative', 'description' => 'Explore creative development needs — from concept to execution.', 'duration_minutes' => 45, 'price' => 200.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 15],
            ['name' => 'Media & Video Production', 'slug' => 'media', 'description' => 'Plan your video, media, or content production pipeline.', 'duration_minutes' => 60, 'price' => 300.00, 'is_free' => false, 'is_active' => false, 'sort_order' => 16],
        ];

        foreach ($types as $type) {
            ConsultType::updateOrCreate(['slug' => $type['slug']], $type);
        }

        // Default availability: Monday (1) through Friday (5), 9am–5pm
        for ($day = 1; $day <= 5; $day++) {
            BookingAvailability::updateOrCreate(
                ['day_of_week' => $day],
                ['start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_active' => true]
            );
        }
    }
}
