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
            'name' => 'Admin User', // دمج الاسم الأول والآخر
            'last_name' => 'User', // حسب الجدول موجود
            'email' => 'admin@example.com',
            'phone_number' => '0000000000',
            'role' => UserRole::ADMIN, // أو 'admin' حسب ما مخزن بالDB
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }
}
