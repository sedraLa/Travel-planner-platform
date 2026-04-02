<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        Destination::query()->get()->each(function (Destination $destination) {
            $hotels = [
                [
                    'name' => "{$destination->city} Heritage Hotel",
                    'description' => 'Comfortable stay near main landmarks.',
                    'global_rating' => '4',
                    'price_per_night' => 120,
                    'stars' => 4,
                ],
                [
                    'name' => "{$destination->city} Boutique Suites",
                    'description' => 'Boutique hotel with local design style.',
                    'global_rating' => '5',
                    'price_per_night' => 180,
                    'stars' => 5,
                ],
                [
                    'name' => "{$destination->city} Budget Inn",
                    'description' => 'Affordable option for short stays.',
                    'global_rating' => '3',
                    'price_per_night' => 70,
                    'stars' => 3,
                ],
            ];

            foreach ($hotels as $hotel) {
                Hotel::updateOrCreate(
                    [
                        'destination_id' => $destination->id,
                        'name' => $hotel['name'],
                    ],
                    [
                        'description' => $hotel['description'],
                        'address' => "Main Street, {$destination->city}",
                        'city' => $destination->city,
                        'country' => $destination->country,
                        'global_rating' => $hotel['global_rating'],
                        'price_per_night' => $hotel['price_per_night'],
                        'total_rooms' => 80,
                        'stars' => $hotel['stars'],
                        'amenities' => ['wifi', 'breakfast', 'air_conditioning'],
                        'pets_allowed' => false,
                        'check_in_time' => '14:00:00',
                        'check_out_time' => '12:00:00',
                        'policies' => 'Standard cancellation policy applies.',
                        'phone_number' => '+10000000000',
                        'email' => 'hotel@example.com',
                        'website' => 'https://example.com',
                        'nearby_landmarks' => 'City center',
                    ]
                );
            }
        });
    }
}

