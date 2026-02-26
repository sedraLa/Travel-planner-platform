<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HotelImage>
 */
class HotelImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'image_url' => fake()->imageUrl(),
            'is_primary' => fake()->boolean(),
        ];
    }
}
