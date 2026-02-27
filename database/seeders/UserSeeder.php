<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 5) as $index) {
            User::updateOrCreate(
                ['email' => "user{$index}@example.com"],
                [
                    'name' => "User{$index}",
                    'last_name' => 'Seeded',
                    'phone_number' => '09' . str_pad((string) $index, 8, '0', STR_PAD_LEFT),
                    'country' => 'Morocco',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role' => UserRole::USER->value,
                ]
            );
        }
    }
}
