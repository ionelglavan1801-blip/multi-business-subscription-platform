<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement(['Free', 'Pro', 'Enterprise']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'price_monthly' => fake()->randomElement([0, 2900, 9900]),
            'max_businesses' => fake()->randomElement([1, 5, 999]),
            'max_users_per_business' => fake()->randomElement([3, 10, 999]),
            'max_projects' => fake()->randomElement([3, 50, 999]),
            'stripe_price_id' => fake()->optional()->regexify('price_[A-Za-z0-9]{24}'),
        ];
    }
}
