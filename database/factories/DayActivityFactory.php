<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\TripDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DayActivity>
 */
class DayActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trip_day_id' => TripDay::factory(),
            'activity_id' => Activity::factory(),
            'custom_activity' => fake()->sentence(),
        ];
    }
}
