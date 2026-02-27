<?php

namespace Database\Seeders;

use App\Models\DayActivity;
use Illuminate\Database\Seeder;

class DayActivitySeeder extends Seeder
{
    public function run(): void
    {
        DayActivity::factory(5)->create();
    }
}
