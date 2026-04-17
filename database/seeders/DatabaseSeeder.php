<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'last_name' => 'User',
                'phone_number' => '0000000000',
                'role' => UserRole::ADMIN->value,
                'country' => 'Morocco',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );
        $this->call([
            UserSeeder::class,
            DestinationSeeder::class,
            DestinationImageSeeder::class,
            HotelSeeder::class,
            HotelImageSeeder::class,
            ActivitySeeder::class,
            HighlightSeeder::class,
            FavoriteSeeder::class,
            TransportVehicleSeeder::class,
            ShiftTemplateSeeder::class,
            AssignmentSeeder::class,
            DriverSeeder::class,
            TransportReservationSeeder::class,
            ReservationSeeder::class,
            PaymentSeeder::class,
            GuideSeeder::class,


        ]);
    }
}
