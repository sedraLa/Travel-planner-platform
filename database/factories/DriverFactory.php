<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory() ,
            'age' => fake()->numberBetween(21,60),
            'address' => fake()->address(),
            'license_image' => fake()->uuid().'.jpg',

            'experience' => fake()->randomElement([
                '1 year experience',
                '2 years experience',
                '3 years experience',
                '5 years experience',
                '10 years professional driving',
                '7 years tourism transport',
            ]),

            'license_category' => fake()->randomElement(['A','B']),

            'personal_image' => fake()->uuid().'.jpg',

            'date_of_hire' => fake()->date(),

            'status' => fake()->randomElement([
                'pending',
                'approved',
                'rejected',
            ]),
        ];
    }
}
