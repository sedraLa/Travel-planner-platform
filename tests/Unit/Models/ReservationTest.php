<?php

namespace Tests\Unit\Models;

use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_relationships_with_payment(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
        ]);

        Payment::factory()->create([
            'reservation_id' => $reservation->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $reservation->hotel());
        $this->assertInstanceOf(BelongsTo::class, $reservation->user());
        $this->assertInstanceOf(HasOne::class, $reservation->payment());

        $this->assertTrue($reservation->hotel->is($hotel));
        $this->assertTrue($reservation->user->is($user));
        $this->assertNotNull($reservation->payment);
    }
}
