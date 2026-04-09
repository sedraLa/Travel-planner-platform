<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'City Tours',
            'Historical Tours',
            'Nature Exploration',
            'Museum Tours',
            'Food Tours',
        ];

        foreach ($names as $name) {
            Specialization::query()->updateOrCreate(['name' => $name], ['name' => $name]);
        }
    }
}
