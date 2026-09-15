<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Contribution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contribution>
 */
class ContributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'board_id' => Board::factory(),
            'user_id' => User::factory(),
            'strokes' => [
                [
                    'color' => fake()->hexColor(),
                    'width' => fake()->numberBetween(2, 8),
                    'points' => [
                        ['x' => fake()->numberBetween(0, 1200), 'y' => fake()->numberBetween(0, 800)],
                        ['x' => fake()->numberBetween(0, 1200), 'y' => fake()->numberBetween(0, 800)],
                    ],
                ],
            ],
            'layer_order' => fake()->numberBetween(0, 20),
        ];
    }
}
