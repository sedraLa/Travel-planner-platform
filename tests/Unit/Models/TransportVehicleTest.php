<?php

namespace Tests\Unit\Models;

use App\Models\Driver;
use App\Models\TransportReservation;
use App\Models\TransportVehicle;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportVehicleTest extends TestCase
{
    use RefreshDatabase;

    public function test_transport_vehicle_relationships_with_counts(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = TransportVehicle::factory()->create(['driver_id' => $driver->id]);

        TransportReservation::factory()->count(2)->create(['transport_vehicle_id' => $vehicle->id]);

        $this->assertInstanceOf(HasMany::class, $vehicle->reservations());
        $this->assertInstanceOf(BelongsTo::class, $vehicle->driver());

        $this->assertTrue($vehicle->driver->is($driver));
        $this->assertCount(2, $vehicle->reservations);
    }
}
