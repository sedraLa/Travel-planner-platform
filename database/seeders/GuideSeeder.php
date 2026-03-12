<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialization;
use App\Models\User;
use App\Models\Guide;
use App\Enums\UserRole;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guides = Guide::factory()->count(10)->create();
        $specializations = Specialization::all();

        foreach($guides as $guide) {
            $guide->specializations()->attach(
                $specializations->random(rand(1,3))->pluck('id')
            );
        }
    }
}
