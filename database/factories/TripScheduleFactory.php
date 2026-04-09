<?php

namespace Database\Factories;

use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TripSchedule>
 */
class TripScheduleFactory extends Factory
{
    public function definition(): array
    {
        $start = CarbonImmutable::parse('2026-06-15')->addDays(fake()->numberBetween(0, 30));
        $duration = fake()->numberBetween(1, 7);

        return [
            'trip_id' => Trip::factory(),
            'start_date' => $start->toDateString(),
            'end_date' => $start->addDays($duration - 1)->toDateString(),
            'booking_deadline' => $start->subDays(7)->toDateString(),
            'available_seats' => fake()->numberBetween(4, 24),
            'price_modifier' => fake()->randomFloat(2, -15, 25),
            'status' => 'available',
        ];
    }
}
