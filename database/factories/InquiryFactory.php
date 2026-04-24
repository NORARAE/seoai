<?php

namespace Database\Factories;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->name(),
            'company'     => fake()->company(),
            'email'       => fake()->unique()->safeEmail(),
            'website'     => fake()->url(),
            'type'        => fake()->randomElement(['agency', 'business', 'both']),
            'tier'        => fake()->randomElement(['starter', '5k', '10k', 'legacy']),
            'niche'       => fake()->word(),
            'message'     => fake()->paragraph(),
            'ip_address'  => fake()->ipv4(),
            'status'      => 'new',
            'spam_risk'   => 'low',
        ];
    }
}
