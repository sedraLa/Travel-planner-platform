<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
    'name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@gmail.com',
    'phone_number' => '0000000000',
    'role' => 'admin',
    'country' => 'Morocco', // أو أي قيمة
    'email_verified_at' => now(),
    'password' => Hash::make('password'),
]);

    }
}
