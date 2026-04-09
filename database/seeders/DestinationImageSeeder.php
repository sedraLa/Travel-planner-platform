<?php

namespace Database\Seeders;

use App\Models\DestinationImage;
use Illuminate\Database\Seeder;

class DestinationImageSeeder extends Seeder
{
    public function run(): void
    {
        DestinationImage::factory(5)->create();
    }
}