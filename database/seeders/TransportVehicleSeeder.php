<?php

namespace Database\Seeders;

use App\Models\TransportVehicle;
use Illuminate\Database\Seeder;

class TransportVehicleSeeder extends Seeder
{
    public function run(): void
    {
        TransportVehicle::factory(5)->create();
    }
}
