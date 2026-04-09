<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Guide;
use App\Models\GuideAssignment;
use App\Models\Specialization;
use App\Models\Trip;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guide>
 */
class GuideFactory extends Factory
{
    protected $model = Guide::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => UserRole::GUIDE->value,
            ]),
            'bio' => fake()->paragraph(),
            'languages' => fake()->randomElement([
                'English',
                'English, Arabic',
                'English, French',
                'English, Spanish',
            ]),
            'years_of_experience' => fake()->numberBetween(1, 15),
            'certificate_image' => 'certificates/sample.jpg',
            'status' => 'pending',
            'earnings_balance' => fake()->randomFloat(2, 0, 5000),
            'personal_image' => 'guides/profile.jpg',
            'age' => fake()->numberBetween(22, 50),
            'address' => 'Beirut, Lebanon',
            'date_of_hire' => '2020-01-01',
            'total_trips_count' => 0,
            'last_trip_at' => null,
            'is_tour_leader' => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'date_of_hire' => '2020-01-01',
            'last_trip_at' => CarbonImmutable::parse('2026-04-01 10:00:00'),
        ]);
    }

    public function accepted(): static
    {
        return $this->approved();
    }

    public function tourLeader(): static
    {
        return $this->state(fn () => [
            'is_tour_leader' => true,
        ]);
    }

    public function locatedIn(string $city, string $country): static
    {
        return $this->state(fn () => [
            'address' => "{$city}, {$country}",
        ]);
    }

    public function recentlyAssigned(int $daysAgo = 3): static
    {
        return $this->state(fn () => [
            'last_trip_at' => now()->subDays($daysAgo),
        ]);
    }

    public function rested(int $daysAgo = 10): static
    {
        return $this->state(fn () => [
            'last_trip_at' => now()->subDays($daysAgo),
        ]);
    }

    public function withSpecializations(array $specializationIds): static
    {
        return $this->afterCreating(function (Guide $guide) use ($specializationIds): void {
            $ids = collect($specializationIds)->filter()->map(fn ($id) => (int) $id)->values()->all();

            if (! empty($ids)) {
                $guide->specializations()->syncWithoutDetaching($ids);
            }
        });
    }

    public function matchingTrip(Trip $trip): static
    {
        return $this
            ->approved()
            ->state(fn () => [
                'address' => trim(($trip->primaryDestination?->city ?? 'Beirut') . ', ' . ($trip->primaryDestination?->country ?? 'Lebanon'), ', '),
                'is_tour_leader' => (bool) $trip->requires_tour_leader,
                'last_trip_at' => now()->subDays(10),
            ])
            ->afterCreating(function (Guide $guide) use ($trip): void {
                $specializationIds = collect($trip->guide_specialization_ids ?? [])->map(fn ($id) => (int) $id)->filter()->values();

                if ($specializationIds->isEmpty()) {
                    $specializationIds = collect([
                        Specialization::factory()->create(['name' => 'City Tours'])->id,
                    ]);
                }

                $guide->specializations()->syncWithoutDetaching($specializationIds->all());
            });
    }

    public function nonMatchingSpecialization(Trip $trip): static
    {
        return $this->approved()->afterCreating(function (Guide $guide) use ($trip): void {
            $required = collect($trip->guide_specialization_ids ?? [])->map(fn ($id) => (int) $id)->filter();

            $specialization = Specialization::factory()->create();
            if ($required->contains($specialization->id)) {
                $specialization = Specialization::factory()->create();
            }

            $guide->specializations()->sync([$specialization->id]);
        });
    }

    public function unavailableForTrip(Trip $trip): static
    {
        return $this->matchingTrip($trip)->afterCreating(function (Guide $guide) use ($trip): void {
            $schedule = $trip->schedules()->orderBy('start_date')->first();

            if (! $schedule) {
                return;
            }

            $conflictingTrip = Trip::factory()
                ->staffingWindow($schedule->start_date, 2)
                ->create([
                    'destination_id' => $trip->destination_id,
                    'status' => 'published',
                ]);

            GuideAssignment::query()->create([
                'trip_id' => $conflictingTrip->id,
                'guide_id' => $guide->id,
                'status' => 'assigned',
            ]);
        });
    }
}
