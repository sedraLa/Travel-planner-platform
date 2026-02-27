<?php

namespace Database\Seeders;

use App\Models\TripDay;
use Illuminate\Database\Seeder;

class TripDaySeeder extends Seeder
{
    public function run(): void
    {
        TripDay::factory(5)->create();
    }
}
