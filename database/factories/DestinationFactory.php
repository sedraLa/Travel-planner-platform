<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    public function definition(): array
    {
        $profiles = [
            ['name' => 'Beirut', 'country' => 'Lebanon', 'iata' => 'BEY', 'timezone' => 'Asia/Beirut', 'currency' => 'LBP', 'language' => 'ar'],
            ['name' => 'Paris', 'country' => 'France', 'iata' => 'CDG', 'timezone' => 'Europe/Paris', 'currency' => 'EUR', 'language' => 'fr'],
            ['name' => 'Istanbul', 'country' => 'Turkey', 'iata' => 'IST', 'timezone' => 'Europe/Istanbul', 'currency' => 'TRY', 'language' => 'tr'],
            ['name' => 'Rome', 'country' => 'Italy', 'iata' => 'FCO', 'timezone' => 'Europe/Rome', 'currency' => 'EUR', 'language' => 'it'],
        ];
        $profile = fake()->randomElement($profiles);

        $cityName = $profile['name'] . ' ' . fake()->unique()->numberBetween(1, 9999);

        return [
            'name' => $cityName,
            'city' => $profile['name'],
            'country' => $profile['country'],
            'description' => fake()->sentence(16),
            'location_details' => fake()->streetAddress(),
            'iata_code' => $profile['iata'],
            'timezone' => $profile['timezone'],
            'language' => $profile['language'],
            'currency' => $profile['currency'],
            'nearest_airport' => "{$profile['name']} International Airport",
            'best_time_to_visit' => fake()->randomElement(['Spring', 'Summer', 'Autumn']),
            'emergency_numbers' => '112',
            'local_tip' => fake()->sentence(),
        ];
    }
}
