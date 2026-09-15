<?php

namespace Database\Factories;

use App\Models\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Board>
 */
class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Collective Artwork #'.fake()->unique()->numberBetween(1, 100000),
            'width' => 1200,
            'height' => 800,
            'background_color' => '#ffffff',
            'max_contributions' => 12,
            'status' => 'open',
        ];
    }
}
