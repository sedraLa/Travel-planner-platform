<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'image' => fake()->imageUrl(),
            'destination_id' => Destination::factory(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(1, 8),
            'duration_unit' => fake()->randomElement(['minutes', 'hours', 'days']),
            'price' => fake()->randomFloat(2, 5, 500),
            'category' => fake()->randomElement(Category::cases())->value,
            'is_active' => true,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'availability' => 'available',
            'guide_name' => fake()->name(),
            'guide_language' => 'en',
            'contact_number' => fake()->phoneNumber(),
            'requirements' => fake()->sentence(),
            'difficulty_level' => fake()->randomElement(['easy', 'moderate', 'hard']),
            'amenities' => ['water', 'equipment'],
            'address' => fake()->address(),
            'requires_booking' => fake()->boolean(),
            'family_friendly' => fake()->randomElement(['yes', 'no']),
            'pets_allowed' => fake()->boolean(),
            'highlights' => fake()->sentence(),
        ];
    }
}
