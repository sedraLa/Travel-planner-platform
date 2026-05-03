<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\TransportVehicle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransportReservationFactory extends Factory
{
    public function definition(): array
    {
        $vehicle = TransportVehicle::factory()->create(); 
    
        return [
            'user_id' => User::factory(),
    
            'pickup_location' => fake()->streetAddress(),
            'dropoff_location' => fake()->streetAddress(),
    
            'pickup_datetime' => now()->addHour(),
            'dropoff_datetime' => now()->addHours(2),
    
            'transport_vehicle_id' => $vehicle->id,  
            'passengers' => fake()->numberBetween(1, $vehicle->max_passengers),
            'preferred_category' => $vehicle->category,
            'preferred_type' => $vehicle->type,
    
            'status' => 'pending_driver',
            'total_price' => fake()->randomFloat(2, 50, 400),
    
            'driver_id' => null,
            'driver_status' => 'pending',
            'driver_earning' => 0,
        ];
    }



    public function pendingDriver()
    {
        return $this->state(fn () => [
            'status' => 'pending_driver',
        ]);
    }

    public function driverAssigned()
    {
        return $this->state(fn () => [
            'status' => 'driver_assigned',
            'driver_id' => Driver::factory(),
            'driver_status' => 'accepted',
        ]);
    }

    public function pendingPayment()
    {
        return $this->state(fn () => [
            'status' => 'pending_payment',
        ]);
    }

    public function confirmed()
    {
        return $this->state(fn () => [
            'status' => 'confirmed',
        ]);
    }

    public function cancelled()
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
        ]);
    }
}