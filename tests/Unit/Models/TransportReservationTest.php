<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\TransportReservation;
use App\Models\TransportVehicle;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transport_reservation_relationships_with_payment(): void
    {
        $user = User::factory()->create();
        $vehicle = TransportVehicle::factory()->create();

        $transportReservation = TransportReservation::factory()->create([
            'user_id' => $user->id,
            'transport_vehicle_id' => $vehicle->id,
        ]);

        Payment::factory()->create([
            'transport_reservation_id' => $transportReservation->id,
            'reservation_id' => null,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $transportReservation->user());
        $this->assertInstanceOf(BelongsTo::class, $transportReservation->vehicle());
        $this->assertInstanceOf(HasOne::class, $transportReservation->payment());
        $this->assertInstanceOf(BelongsTo::class, $transportReservation->driver());

        $this->assertTrue($transportReservation->user->is($user));
        $this->assertTrue($transportReservation->vehicle->is($vehicle));
        $this->assertNotNull($transportReservation->payment);
    }
}
