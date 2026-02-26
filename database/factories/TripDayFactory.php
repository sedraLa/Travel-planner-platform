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
        return [
            'trip_id' => Trip::factory(),
            'day_number' => fake()->numberBetween(1, 14),
            'hotel_id' => Hotel::factory(),
            'custom_hotel' => fake()->company() . ' Inn',
        ];
    }
}
