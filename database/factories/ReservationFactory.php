<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'hotel_id' => Hotel::factory(),
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'rooms_count' => fake()->numberBetween(1, 3),
            'guest_count' => fake()->numberBetween(1, 6),
            'total_price' => fake()->randomFloat(2, 100, 3000),
            'reservation_status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }
}
