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
    public function configure(): static
    {
        return $this->afterMaking(function ($activity) {
            if ($activity->destination) {
                $activity->address = fake()->streetAddress() . ', ' . $activity->destination->city;
            }
        });
    }

    public function definition(): array
    {
        $category = fake()->randomElement(Category::cases())->value;
        $namesByCategory = [
            'culture' => ['City Museum Tour', 'Historic District Walk', 'Traditional Crafts Workshop'],
            'nature' => ['Coastal Sunset Walk', 'Mountain Trail Hike', 'Botanical Garden Visit'],
            'shopping' => ['Old Souk Shopping', 'Artisan Market Visit', 'Local Boutique Crawl'],
            'sports' => ['Kayaking Session', 'Cycling City Route', 'Rock Climbing Experience'],
            'entertainment' => ['Live Music Night', 'Food Festival Visit', 'City Theater Show'],
        ];

        return [
            'name' => fake()->randomElement($namesByCategory[$category]),
            'image' => fake()->imageUrl(),
            'destination_id' => Destination::factory(),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(1, 4),
            'duration_unit' => 'hours',
            'price' => fake()->randomFloat(2, 5, 500),
            'category' => $category,
            'is_active' => true,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
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
