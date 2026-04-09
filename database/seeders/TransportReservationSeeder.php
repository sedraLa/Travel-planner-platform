<?php

namespace Database\Seeders;

use App\Models\TransportReservation;
use Illuminate\Database\Seeder;

class TransportReservationSeeder extends Seeder
{
    public function run(): void
    {
        TransportReservation::factory(5)->create();
    }
}