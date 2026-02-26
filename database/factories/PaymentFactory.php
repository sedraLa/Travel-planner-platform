<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\TransportReservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'transport_reservation_id' => null,
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 20, 5000),
            'status' => fake()->randomElement(['paid', 'pending', 'failed']),
            'transaction_id' => fake()->uuid(),
            'payment_date' => now()->toDateString(),
        ];
    }

    public function forTransportReservation(): static
    {
        return $this->state(fn () => [
            'transport_reservation_id' => TransportReservation::factory(),
            'reservation_id' => null,
        ]);
    }
}
