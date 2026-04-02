<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Beirut Downtown',
                'city' => 'Beirut',
                'country' => 'Lebanon',
                'description' => 'Historic downtown with museums, local culture, and sea views.',
                'location_details' => 'Downtown district',
                'iata_code' => 'BEY',
                'timezone' => 'Asia/Beirut',
                'language' => 'ar',
                'currency' => 'LBP',
                'nearest_airport' => 'Rafic Hariri International Airport',
                'best_time_to_visit' => 'Spring',
                'emergency_numbers' => '112',
                'local_tip' => 'Try local breakfast early in the morning.',
            ],
            [
                'name' => 'Istanbul Old City',
                'city' => 'Istanbul',
                'country' => 'Turkey',
                'description' => 'Rich historical sites, bazaars, and Bosphorus experiences.',
                'location_details' => 'Sultanahmet area',
                'iata_code' => 'IST',
                'timezone' => 'Europe/Istanbul',
                'language' => 'tr',
                'currency' => 'TRY',
                'nearest_airport' => 'Istanbul Airport',
                'best_time_to_visit' => 'Autumn',
                'emergency_numbers' => '112',
                'local_tip' => 'Buy museum pass to save time and money.',
            ],
            [
                'name' => 'Paris Center',
                'city' => 'Paris',
                'country' => 'France',
                'description' => 'Iconic museums, cafés, and romantic evening spots.',
                'location_details' => 'Central arrondissements',
                'iata_code' => 'CDG',
                'timezone' => 'Europe/Paris',
                'language' => 'fr',
                'currency' => 'EUR',
                'nearest_airport' => 'Charles de Gaulle Airport',
                'best_time_to_visit' => 'Spring',
                'emergency_numbers' => '112',
                'local_tip' => 'Book popular museum slots in advance.',
            ],
        ];

        foreach ($destinations as $destinationData) {
            Destination::updateOrCreate(
                ['name' => $destinationData['name']],
                $destinationData
            );
        }
    }
}

