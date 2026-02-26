<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransportVehicle>
 */
class TransportVehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_model' => fake()->company() . ' ' . fake()->word(),
            'plate_number' => strtoupper(fake()->bothify('??-####')),
            'driver_id' => Driver::factory(),
            'max_passengers' => fake()->numberBetween(2, 8),
            'base_price' => fake()->randomFloat(2, 20, 150),
            'price_per_km' => fake()->randomFloat(2, 1, 10),
            'category' => fake()->randomElement(['economy', 'comfort', 'vip']),
            'image' => fake()->imageUrl(),
            'type' => fake()->randomElement(['sedan', 'suv', 'van']),
        ];
    }
}
