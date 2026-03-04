<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Assignment;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        Driver::factory(5)->create();
    }
}
