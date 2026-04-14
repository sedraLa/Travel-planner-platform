<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Destination;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        Destination::query()->get()->each(function (Destination $destination) {
            $activities = [
                ['name' => 'City Museum Tour', 'category' => 'culture', 'price' => 20, 'duration' => 2],
                ['name' => 'Historical Walking Tour', 'category' => 'culture', 'price' => 35, 'duration' => 3],
                ['name' => 'Botanical Garden Visit', 'category' => 'nature', 'price' => 15, 'duration' => 2],
                ['name' => 'Coastal Sunset Walk', 'category' => 'nature', 'price' => 10, 'duration' => 2],
                ['name' => 'Local Souk Shopping', 'category' => 'shopping', 'price' => 0, 'duration' => 3],
                ['name' => 'Artisan Market Visit', 'category' => 'shopping', 'price' => 5, 'duration' => 2],
                ['name' => 'Cycling City Route', 'category' => 'sports', 'price' => 25, 'duration' => 2],
                ['name' => 'Kayaking Session', 'category' => 'sports', 'price' => 30, 'duration' => 2],
                ['name' => 'Evening Cultural Show', 'category' => 'entertainment', 'price' => 40, 'duration' => 2],
                ['name' => 'Food Festival Experience', 'category' => 'entertainment', 'price' => 18, 'duration' => 2],
                ['name' => 'Family Theme Park Day', 'category' => 'family', 'price' => 45, 'duration' => 6],
                ['name' => 'Romantic Sunset Dinner', 'category' => 'romance', 'price' => 80, 'duration' => 3],
                ['name' => 'Desert Adventure Safari', 'category' => 'adventure', 'price' => 55, 'duration' => 5],
                ['name' => 'Wellness Spa & Yoga Session', 'category' => 'wellness', 'price' => 65, 'duration' => 4],
                ['name' => 'Local Culinary Workshop', 'category' => 'food', 'price' => 35, 'duration' => 3],
            ];

            foreach ($activities as $activity) {
                Activity::updateOrCreate(
                    [
                        'destination_id' => $destination->id,
                        'name' => $activity['name'],
                    ],
                    [
                        'image' => 'https://picsum.photos/seed/activity/600/400',
                        'description' => "{$activity['name']} in {$destination->city}",
                        'duration' => $activity['duration'],
                        'duration_unit' => 'hours',
                        'price' => $activity['price'],
                        'category' => $activity['category'],
                        'is_active' => true,
                        'start_time' => '09:00:00',
                        'end_time' => '11:00:00',
                        'start_date' => now()->toDateString(),
                        'end_date' => now()->addYear()->toDateString(),
                        'availability' => 'available',
                        'guide_name' => 'Local Guide',
                        'guide_language' => 'en',
                        'contact_number' => '+10000000000',
                        'requirements' => 'Comfortable shoes',
                        'difficulty_level' => 'easy',
                        'amenities' => ['water'],
                        'address' => "Tourism Street, {$destination->city}",
                        'requires_booking' => true,
                        'family_friendly' => 'yes',
                        'pets_allowed' => false,
                        'highlights' => "Top rated {$activity['category']} activity",
                    ]
                );
            }
        });
    }
}

