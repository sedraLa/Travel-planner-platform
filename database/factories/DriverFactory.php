<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\ShiftTemplate;
use App\Models\TransportReservation;
use App\Models\TransportVehicle;
use App\Models\Trip;
use App\Models\TripTransport;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => UserRole::DRIVER->value,
            ]),
            'age' => fake()->numberBetween(21, 60),
            'address' => 'Beirut, Lebanon',
            'license_image' => fake()->uuid() . '.jpg',
            'experience' => fake()->randomElement([
                '1 year experience',
                '2 years experience',
                '3 years experience',
                '5 years experience',
                '7 years tourism transport',
                '10 years professional driving',
            ]),
            'license_category' => fake()->randomElement(['A', 'B']),
            'personal_image' => fake()->uuid() . '.jpg',
            'date_of_hire' => '2020-01-01',
            'status' => 'pending',
            'last_trip_at' => CarbonImmutable::parse('2026-03-20 09:00:00'),
            'total_trips_count' => fake()->numberBetween(0, 250),
            'earnings_balance' => fake()->randomFloat(2, 0, 10000),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
        ]);
    }

    public function accepted(): static
    {
        return $this->approved();
    }

    public function locatedIn(string $city, string $country): static
    {
        return $this->state(fn () => [
            'address' => "{$city}, {$country}",
        ]);
    }

    public function withAssignment(?TransportVehicle $vehicle = null): static
    {
        return $this->afterCreating(function (Driver $driver) use ($vehicle): void {
            $assignedVehicle = $vehicle ?? TransportVehicle::factory()->create();

            Assignment::query()->updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'transport_vehicle_id' => $assignedVehicle->id,
                    'shift_template_id' => ShiftTemplate::factory()->create()->id,
                ]
            );
        });
    }

    public function matchingTrip(Trip $trip): static
    {
        return $this
            ->approved()
            ->state(fn () => [
                'address' => trim(($trip->primaryDestination?->city ?? 'Beirut') . ', ' . ($trip->primaryDestination?->country ?? 'Lebanon'), ', '),
                'last_trip_at' => now()->subDays(10),
            ])
            ->afterCreating(function (Driver $driver) use ($trip): void {
                $vehicle = TransportVehicle::factory()->create([
                    'type' => $trip->driver_vehicle_type ?? 'van',
                    'max_passengers' => max((int) ($trip->driver_vehicle_capacity ?? 1), 1),
                ]);

                Assignment::query()->updateOrCreate(
                    ['driver_id' => $driver->id],
                    [
                        'transport_vehicle_id' => $vehicle->id,
                        'shift_template_id' => ShiftTemplate::factory()->create()->id,
                    ]
                );
            });
    }

    public function failingCapacity(Trip $trip): static
    {
        return $this->approved()->afterCreating(function (Driver $driver) use ($trip): void {
            $required = max((int) ($trip->driver_vehicle_capacity ?? 4), 1);

            $vehicle = TransportVehicle::factory()->create([
                'type' => $trip->driver_vehicle_type ?? 'van',
                'max_passengers' => max(1, $required - 1),
            ]);

            Assignment::query()->updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'transport_vehicle_id' => $vehicle->id,
                    'shift_template_id' => ShiftTemplate::factory()->create()->id,
                ]
            );
        });
    }

    public function failingLocation(): static
    {
        return $this->state(fn () => [
            'address' => 'Tokyo, Japan',
        ]);
    }

    public function unavailableForTrip(Trip $trip): static
    {
        return $this->matchingTrip($trip)->afterCreating(function (Driver $driver) use ($trip): void {
            $schedule = $trip->schedules()->orderBy('start_date')->first();

            if (! $schedule) {
                return;
            }

            $vehicleId = Assignment::query()->where('driver_id', $driver->id)->value('transport_vehicle_id');
            if (! $vehicleId) {
                return;
            }

            $conflictingTrip = Trip::factory()
                ->staffingWindow($schedule->start_date, 2)
                ->create([
                    'destination_id' => $trip->destination_id,
                    'status' => 'published',
                ]);

            TripTransport::query()->create([
                'trip_id' => $conflictingTrip->id,
                'transport_vehicle_id' => $vehicleId,
                'driver_id' => $driver->id,
                'transport_type' => 'daily_transport',
                'departure_time' => '09:00:00',
                'return_time' => '17:00:00',
                'notes' => 'Conflicting transport assignment',
            ]);

            TransportReservation::factory()->create([
                'driver_id' => $driver->id,
                'transport_vehicle_id' => $vehicleId,
                'pickup_datetime' => CarbonImmutable::parse($schedule->start_date . ' 10:00:00'),
                'dropoff_datetime' => CarbonImmutable::parse($schedule->end_date . ' 13:00:00'),
                'status' => 'driver_assigned',
            ]);
        });
    }
}
