<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Assignment;
use App\Models\TransportVehicle;
use App\Models\Driver;
use App\Models\ShiftTemplate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_relationships(): void
    {
        //prepare data 
        $vehicle = TransportVehicle::factory()->create();
        $driver = Driver::factory()->create();
        $shiftTemplate = ShiftTemplate::factory()->create();

        $assignment = Assignment::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shiftTemplate->id,
            'driver_id' => $driver->id,
        ]);

        //test relationships
        $this->assertInstanceOf(BelongsTo::class, $assignment->vehicle());
        $this->assertInstanceOf(BelongsTo::class, $assignment->shiftTemplate());
        $this->assertInstanceOf(BelongsTo::class, $assignment->driver());

        //test data relationships
        $this->assertTrue($assignment->vehicle->is($vehicle));
        $this->assertTrue($assignment->driver->is($driver));
        $this->assertTrue($assignment->shiftTemplate->is($shiftTemplate));
    }
}