<?php

namespace Tests\Unit\Models;

use App\Models\Driver;
use App\Models\TransportReservation;
use App\Models\TransportVehicle;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_relationships_with_counts(): void
    {
        $user = User::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        TransportVehicle::factory()->create(['driver_id' => $driver->id]);
        TransportReservation::factory()->count(2)->create(['driver_id' => $driver->id]);

        $this->assertInstanceOf(HasOne::class, $driver->vehicle());
        $this->assertInstanceOf(HasMany::class, $driver->reservations());
        $this->assertInstanceOf(BelongsTo::class, $driver->user());

        $this->assertNotNull($driver->vehicle);
        $this->assertCount(2, $driver->reservations);
        $this->assertTrue($driver->user->is($user));
    }
}
