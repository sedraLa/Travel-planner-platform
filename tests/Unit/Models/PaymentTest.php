<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\TransportReservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_relationships_to_user_and_reservation(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create(['user_id' => $user->id]);

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'reservation_id' => $reservation->id,
            'transport_reservation_id' => null,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $payment->reservation());
        $this->assertInstanceOf(BelongsTo::class, $payment->user());
        $this->assertInstanceOf(BelongsTo::class, $payment->transportReservation());

        $this->assertTrue($payment->user->is($user));
        $this->assertTrue($payment->reservation->is($reservation));
    }

    public function test_payment_transport_reservation_state(): void
    {
        $payment = Payment::factory()->forTransportReservation()->create();

        $this->assertNotNull($payment->transportReservation);
        $this->assertNull($payment->reservation_id);
    }
}
