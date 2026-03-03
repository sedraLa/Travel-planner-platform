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
            'driver_id'=>Driver::factory(),
            'transport_vehicle_id' => TransportVehicle::factory(),
            'shift_template_id' => ShiftTemplate::factory(),
           
        ];
    }
}
