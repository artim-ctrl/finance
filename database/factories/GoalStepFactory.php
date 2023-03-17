<?php

namespace Database\Factories;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Goal>
 */
class GoalStepFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->title,
            'goal_id' => 1,
            'estimated_currency_id' => fake()->numberBetween(1, 4),
            'estimated_amount' => fake()->numberBetween(100, 100000),
            'currency_id' => fake()->randomElement([null, fake()->numberBetween(1, 4)]),
            'amount' => fake()->randomElement([null, fake()->numberBetween(100, 100000)]),
        ];
    }
}
