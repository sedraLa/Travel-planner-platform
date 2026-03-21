<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Specialization;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialization>
 */
class SpecializationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Historical Tours',
                'Mountain Hiking',
                'City Tours',
                'Food Tours',
                'Adventure Trips',
                'Cultural Tours',
                'Nature Exploration',
                'Desert Safari',
                'Wildlife Watching',
                'Museum Tours',
                'Religious Tours',
                'Photography Tours',
                'Beach Tours',
                'Night City Tours',
                'Luxury Private Tours',
                'Family Friendly Tours',
                'Eco Tourism',
                'Local Village Tours',
                'Archaeological Tours',
                'Boat Tours',
                'Cycling Tours',
                'Backpacking Tours',
                'Shopping Tours',
                'Festival Tours'
            ])
        ];
    }
}
