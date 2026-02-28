<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShiftTemplate>
 */
class ShiftTemplateFactory extends Factory
{
    public function definition(): array
    {
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        return [
            'name' => fake()->randomElement(['Morning Shift', 'Evening Shift', 'Weekend Shift']),
            'start_time' => fake()->time('H:i:s'),
            'end_time' => fake()->time('H:i:s'),
            'days_of_week' => fake()->randomElements($days, fake()->numberBetween(2, 5)),
        ];
    }
}
