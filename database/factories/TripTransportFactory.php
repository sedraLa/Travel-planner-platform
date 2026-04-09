<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\TransportVehicle;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripTransport>
 */
class TripTransportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'transport_vehicle_id' => TransportVehicle::factory(),
            'driver_id' => null,
            'transport_type' => fake()->randomElement(['arrival_transfer', 'daily_transport', 'departure_transfer']),
            'departure_time' => '08:00:00',
            'return_time' => '18:00:00',
            'notes' => fake()->sentence(8),
        ];
    }

    public function withDriver(Driver $driver): static
    {
        return $this->state(fn () => [
            'driver_id' => $driver->id,
        ]);
    }
}
