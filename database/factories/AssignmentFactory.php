<?php

namespace Database\Factories;

use App\Models\ShiftTemplate;
use App\Models\Driver;
use App\Models\TransportVehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory()->approved(),
            'transport_vehicle_id' => TransportVehicle::factory(),
            'shift_template_id' => ShiftTemplate::factory(),
           
        ];
    }

    public function forDriver($driver)
    {
        return $this->state(fn () => [
            'driver_id' => $driver->id,
        ]);
    }

    public function forVehicle($vehicle)
    {
        return $this->state(fn () => [
            'transport_vehicle_id' => $vehicle->id,
        ]);
    }

    public function forShift($shift)
    {
        return $this->state(fn () => [
            'shift_template_id' => $shift->id,
        ]);
    }
}