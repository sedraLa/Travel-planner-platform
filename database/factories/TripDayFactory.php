<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripDay>
 */
class TripDayFactory extends Factory
{
    public function definition(): array
    {
        $dayNumber = fake()->numberBetween(1, 7);

        return [
            'trip_id' => Trip::factory(),
            'day_number' => $dayNumber,
            'title' => "Day {$dayNumber}",
            'description' => fake()->sentence(10),
            'highlights' => [fake()->sentence(4), fake()->sentence(4)],
            'hotel_id' => Hotel::factory(),
        ];
    }
}
