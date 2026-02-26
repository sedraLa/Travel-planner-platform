<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'is_ai' => fake()->boolean(),
            'destination_id' => Destination::factory(),
            'destination_name' => fake()->city(),
            'name' => 'Trip to ' . fake()->city(),
            'description' => fake()->sentence(),
            'travelers_number' => fake()->numberBetween(1, 8),
            'budget' => fake()->randomFloat(2, 200, 10000),
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'flight_number' => strtoupper(fake()->bothify('??###')),
            'airline' => fake()->company(),
            'departure_airport' => strtoupper(fake()->lexify('???')),
            'arrival_airport' => strtoupper(fake()->lexify('???')),
            'departure_time' => now(),
            'arrival_time' => now()->addHours(2),
            'ai_itinerary' => fake()->paragraph(),
        ];
    }
}
