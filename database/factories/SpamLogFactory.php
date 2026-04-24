<?php

namespace Database\Factories;

use App\Models\Inquiry;
use App\Models\SpamLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpamLog>
 */
class SpamLogFactory extends Factory
{
    protected $model = SpamLog::class;

    public function definition(): array
    {
        $action = fake()->randomElement(['block', 'flag']);

        return [
            'inquiry_id'       => Inquiry::factory(),
            'action'           => $action,
            'reason'           => $action === 'block'
                ? fake()->randomElement(['antispam_blocked', 'honeypot_triggered', 'high_risk_score', 'duplicate_submission'])
                : fake()->randomElement(['antispam_flagged', 'medium_risk_allowed']),
            'spam_risk'        => $action === 'block' ? 'high' : 'medium',
            'risk_score'       => $action === 'block' ? fake()->numberBetween(6, 15) : fake()->numberBetween(3, 5),
            'ip_address'       => fake()->ipv4(),
            'email'            => fake()->safeEmail(),
            'name'             => fake()->name(),
            'company'          => fake()->company(),
            'user_agent'       => fake()->userAgent(),
            'turnstile_valid'  => null,
            'turnstile_reason' => null,
            'is_reviewed'      => false,
            'signals'          => ['blocked_company_name'],
        ];
    }
}
