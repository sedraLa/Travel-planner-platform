<?php

namespace Database\Factories;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterMaking(function ($hotel) {
            if ($hotel->destination) {
                $hotel->city = $hotel->destination->city;
                $hotel->country = $hotel->destination->country;
                $hotel->address = fake()->streetAddress() . ', ' . $hotel->destination->city;
            }
        });
    }

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Hotel',
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'global_rating' => (string) fake()->numberBetween(1, 5),
            'price_per_night' => fake()->randomFloat(2, 50, 600),
            'total_rooms' => fake()->numberBetween(10, 250),
            'destination_id' => Destination::factory(),
            'stars' => fake()->numberBetween(1, 5),
            'amenities' => ['wifi', 'parking'],
            'pets_allowed' => fake()->boolean(),
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'policies' => fake()->sentence(),
            'phone_number' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'website' => fake()->url(),
            'nearby_landmarks' => fake()->sentence(),
        ];
    }
}
