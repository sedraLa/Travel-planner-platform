<?php

namespace Database\Factories;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Highlight>
 */
class HighlightFactory extends Factory
{
    public function definition(): array
    {
        return [
            'destination_id' => Destination::factory(),
            'title' => fake()->sentence(4),
        ];
    }
}
