<?php

namespace Database\Factories;

use App\Models\ShiftTemplate;
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
            'transport_vehicles_id' => TransportVehicle::factory(),
            'shift_template_id' => ShiftTemplate::factory(),
        ];
    }
}
