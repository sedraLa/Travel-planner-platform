<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    private const DEFAULT_START_DATE = '2026-06-15';

    protected $model = Trip::class;

    public function definition(): array
    {
        $name = sprintf('Trip %04d', fake()->unique()->numberBetween(1, 9999));

        return [
            'destination_id' => Destination::factory(),
            'name' => $name,
            'slug' => str($name)->slug() . '-' . fake()->unique()->numerify('##'),
            'description' => fake()->paragraph(),
            'duration_days' => 3,
            'category' => fake()->randomElement(['adventure', 'cultural', 'family', 'city']),
            'max_participants' => 12,
            'meeting_point_description' => 'Main square meeting point',
            'meeting_point_address' => 'Downtown district',
            'is_ai_generated' => false,
            'ai_prompt' => null,
            'status' => 'ready_for_assignment',
            'guide_specialization_ids' => [],
            'requires_tour_leader' => true,
            'driver_vehicle_type' => 'van',
            'driver_vehicle_capacity' => 8,
            'driver_trip_type' => 'intercity',
            'driver_road_type' => 'highway',
            'ranked_guide_ids' => null,
            'ranked_driver_ids' => null,
            'assigned_guide_id' => null,
            'assigned_driver_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this
            ->afterCreating(function (Trip $trip): void {
                if ($trip->schedules()->exists()) {
                    return;
                }

                $start = CarbonImmutable::parse(self::DEFAULT_START_DATE);
                $duration = max(1, (int) $trip->duration_days);

                $trip->schedules()->create([
                    'start_date' => $start->toDateString(),
                    'end_date' => $start->addDays($duration - 1)->toDateString(),
                    'booking_deadline' => $start->subDays(10)->toDateString(),
                    'available_seats' => $trip->max_participants,
                    'price_modifier' => 0,
                    'status' => 'available',
                ]);

                if (! $trip->days()->exists()) {
                    for ($day = 1; $day <= $duration; $day++) {
                        $trip->days()->create([
                            'day_number' => $day,
                            'title' => "Day {$day}",
                            'description' => "Activities planned for day {$day}.",
                            'highlights' => ["Highlight {$day}"],
                        ]);
                    }
                }
            });
    }

    public function withMultipleSchedules(int $count = 2, ?string $firstStartDate = null): static
    {
        return $this->afterCreating(function (Trip $trip) use ($count, $firstStartDate): void {
            $trip->schedules()->delete();

            $start = CarbonImmutable::parse($firstStartDate ?? self::DEFAULT_START_DATE);
            $duration = max(1, (int) $trip->duration_days);

            for ($index = 0; $index < max(1, $count); $index++) {
                $windowStart = $start->addDays($index * ($duration + 2));

                $trip->schedules()->create([
                    'start_date' => $windowStart->toDateString(),
                    'end_date' => $windowStart->addDays($duration - 1)->toDateString(),
                    'booking_deadline' => $windowStart->subDays(7)->toDateString(),
                    'available_seats' => $trip->max_participants,
                    'price_modifier' => 0,
                    'status' => 'available',
                ]);
            }
        });
    }

    public function staffingWindow(string $startDate, int $durationDays = 3): static
    {
        return $this->state(fn () => [
            'duration_days' => max(1, $durationDays),
        ])->afterCreating(function (Trip $trip) use ($startDate): void {
            $trip->schedules()->delete();

            $start = CarbonImmutable::parse($startDate);
            $duration = max(1, (int) $trip->duration_days);

            $trip->schedules()->create([
                'start_date' => $start->toDateString(),
                'end_date' => $start->addDays($duration - 1)->toDateString(),
                'booking_deadline' => $start->subDays(8)->toDateString(),
                'available_seats' => $trip->max_participants,
                'price_modifier' => 0,
                'status' => 'available',
            ]);

            $trip->days()->delete();
            for ($day = 1; $day <= $duration; $day++) {
                $trip->days()->create([
                    'day_number' => $day,
                    'title' => "Day {$day}",
                    'description' => "Staffing scenario day {$day}",
                    'highlights' => ["Scenario highlight {$day}"],
                ]);
            }
        });
    }
}
