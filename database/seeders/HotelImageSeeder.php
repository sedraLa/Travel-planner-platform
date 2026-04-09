<?php

namespace Database\Seeders;

use App\Models\HotelImage;
use Illuminate\Database\Seeder;

class HotelImageSeeder extends Seeder
{
    public function run(): void
    {
        HotelImage::factory(5)->create();
    }
}