<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\TransportVehicle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransportReservation>
 */
class TransportReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pickup_location' => fake()->streetAddress(),
            'dropoff_location' => fake()->streetAddress(),
            'pickup_datetime' => now()->addHour(),
            'dropoff_datetime' => now()->addHours(2),
            'passengers' => fake()->numberBetween(1, 6),
            'total_price' => fake()->randomFloat(2, 15, 400),
            'status' => fake()->randomElement(['completed', 'pending', 'cancelled']),
            'transport_vehicle_id' => TransportVehicle::factory(),
            'driver_id' => Driver::factory(),
            'driver_status' => fake()->randomElement(['pending', 'accepted', 'rejected', 'completed', 'cancelled']),
            'driver_earning'=>function (array $attributes) {
        return $attributes['total_price'] * 0.2; },



        ];
    }
}
