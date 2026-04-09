<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\ShiftTemplate;
use App\Models\TransportVehicle;
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

}
