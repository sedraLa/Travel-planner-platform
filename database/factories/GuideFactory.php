<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Guide;
use App\Enums\UserRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guide>
 */
class GuideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => UserRole::GUIDE->value,
            ]),

            'bio' => $this->faker->paragraph(),
            'languages' => $this->faker->randomElement([
                'English',
                'English, Arabic',
                'English, French',
                'English, Spanish'
            ]),

            'years_of_experience' => $this->faker->numberBetween(1,15),
            'certificate_image' => 'certificates/sample.jpg',
            'status' => $this->faker->randomElement([
                'pending',
                'approved',
                'rejected'
            ]),
            
            'earnings_balance' => $this->faker->randomFloat(2,0,5000),
            'personal_image' => 'guides/profile.jpg',
            'age' => $this->faker->numberBetween(22,50),
            'address' => $this->faker->address(),
            'date_of_hire' => null,
            'total_trips_count' => 0,
            'last_trip_at' => null,
            'is_tour_leader' => $this->faker->boolean()
        ];
    }

    /**
 * State: guide approved
 */

        public function approved()
        {
            return $this->state(fn()=> [
                'status' => 'approved',
                'address' => $this->faker->address(),
                'date_of_hire' => $this->faker->date(),
                'total_trips_count' => $this->faker->numberBetween(0,100),
                'last_trip_at' => $this->faker->dateTime(),
            ]);
        }
}
