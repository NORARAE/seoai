<?php

namespace Tests\Feature;

use App\Models\QuickScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MultiSystemDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_mode_activates_at_three_scans_and_reduces_competing_sections(): void
    {
        $user = $this->createApprovedUser();

        $this->seedScans($user, 2);

        $this->actingAs($user)
            ->get(route('app.dashboard'))
            ->assertOk()
            ->assertDontSee('System Grid')
            ->assertSee('System Modules')
            ->assertSee('Additional Capabilities')
            ->assertSee('Recommended Actions');

        $this->seedScans($user, 1, 3);

        $this->actingAs($user)
            ->get(route('app.dashboard'))
            ->assertOk()
            ->assertSee('System Grid')
            ->assertDontSee('System Modules')
            ->assertDontSee('Additional Capabilities')
            ->assertSee('Recommended Actions');
    }

    public function test_system_grid_cards_include_required_fields_and_correct_report_links(): void
    {
        $user = $this->createApprovedUser();
        $scans = $this->seedScans($user, 3);

        $response = $this->actingAs($user)->get(route('app.dashboard'));

        $response->assertOk()->assertSee('System Grid');

        foreach ($scans as $scan) {
            $scan->refresh();

            $response
                ->assertSee($scan->domain())
                ->assertSee((string) $scan->score)
                ->assertSee(route('dashboard.scans.show', ['scan' => $scan->systemScanId()]), false);
        }

        $response
            ->assertSee('Most tools optimize content. This shows what gets selected.')
            ->assertSee('Status')
            ->assertSee('Selected')
            ->assertSee('Partial Selection')
            ->assertSee('Not Selected')
            ->assertSee('Needs Attention')
            ->assertSee('Fastest Fix')
            ->assertSee('Fix This')
            ->assertSee('Open System');
    }

    public function test_for_agencies_route_and_page_are_available(): void
    {
        $this->get(route('for-agencies'))
            ->assertOk()
            ->assertSee('Run and manage additional systems without friction.')
            ->assertSee(route('quick-scan.show'), false);
    }

    public function test_grid_density_marker_for_3_4_6_and_10_systems(): void
    {
        $scenarios = [
            3 => 'wide',
            4 => 'wide',
            6 => 'standard',
            10 => 'compact',
        ];

        foreach ($scenarios as $count => $density) {
            $user = $this->createApprovedUser("density{$count}@example.com");
            $this->seedScans($user, $count);

            $this->actingAs($user)
                ->get(route('app.dashboard'))
                ->assertOk()
                ->assertSee('System Grid')
                ->assertSee('data-grid-density="' . $density . '"', false);
        }
    }

    private function createApprovedUser(?string $email = null): User
    {
        return User::factory()->create([
            'email' => $email ?? 'dashboard-user@example.com',
            'approved' => true,
            'onboarding_completed_at' => now(),
            'role' => 'customer',
        ]);
    }

    private function seedScans(User $user, int $count, int $startIndex = 1): Collection
    {
        $scores = [92, 72, 48, 65, 58, 83, 77, 61, 55, 90, 68, 74];
        $created = collect();

        for ($i = 0; $i < $count; $i++) {
            $index = $startIndex + $i;
            $score = $scores[($index - 1) % count($scores)];

            $created->push(QuickScan::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'url' => "https://system{$index}.example.com",
                'status' => QuickScan::STATUS_SCANNED,
                'paid' => true,
                'score' => $score,
                'issues' => $score < 60 ? ['schema missing', 'missing faq'] : ['internal link gap'],
                'page_count' => 10 + $index,
                'scanned_at' => now()->subHours($index),
                'created_at' => now()->subHours($index),
                'updated_at' => now()->subHours($index),
            ]));
        }

        return $created;
    }
}
