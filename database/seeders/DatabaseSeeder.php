<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // <-- استيراد موديل المستخدم
use App\Enums\UserRole; // <-- استيراد الـ Enum الخاص بالأدوار
use Illuminate\Support\Facades\Hash; // <-- استيراد أداة تشفير كلمة المرور

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- هذا هو الكود الذي ينشئ الأدمن ---
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com', // <-- يمكنكِ تغييره لإيميلك
            'phone' => '0000000000',
            'date_of_birth' => '1990-01-01',
            'password' => Hash::make('password'), // <-- غيري 'password' إلى كلمة مرور قوية
            'role' => UserRole::ADMIN, // <-- تحديد دوره كـ ADMIN
            'email_verified_at' => now(), // <-- جعل الإيميل مؤكداً تلقائياً
        ]);
    }
}
