<?php

namespace Database\Factories;

use App\Models\Guide;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GuideAvailability>
 */
class GuideAvailabilityFactory extends Factory
{
    public function definition(): array
    {
        $date = CarbonImmutable::parse('2026-06-15')->addDays(fake()->numberBetween(0, 14));

        return [
            'guide_id' => Guide::factory()->approved(),
            'date' => $date->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ];
    }
}
