<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Enums\UserRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => UserRole::DRIVER->value,
            ]),
            'age' => fake()->numberBetween(21, 60),
            'address' => fake()->address(),
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
            'date_of_hire' => fake()->date(),
            'status' => 'pending', // default منطقي
            'last_trip_at' => fake()->optional()->dateTimeBetween('-3 months', 'now'),
            'total_trips_count' => fake()->numberBetween(0, 250),
            'earnings_balance' => fake()->randomFloat(2, 0, 10000),
        ];
    }

    // State للسائق approved
    public function approved()
    {
        return $this->state(fn () => [
            'status' => 'approved',
        ]);
    }
}