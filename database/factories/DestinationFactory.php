<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city() . ' Destination',
            'city' => fake()->city(),
            'country' => fake()->country(),
            'description' => fake()->paragraph(),
            'location_details' => fake()->address(),
            'iata_code' => strtoupper(fake()->lexify('???')),
            'timezone' => fake()->timezone(),
            'language' => fake()->languageCode(),
            'currency' => fake()->currencyCode(),
            'nearest_airport' => fake()->city() . ' International Airport',
            'best_time_to_visit' => fake()->monthName(),
            'emergency_numbers' => '112',
            'local_tip' => fake()->sentence(),
        ];
    }
}
