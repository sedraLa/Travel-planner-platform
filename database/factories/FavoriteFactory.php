<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite>
 */
class FavoriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'favoritable_type' => Destination::class,
            'favoritable_id' => Destination::factory(),
        ];
    }
}
