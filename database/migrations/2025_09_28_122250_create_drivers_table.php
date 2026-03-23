<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users') 
                  ->onDelete('cascade') 
                  ->after('id');
            $table->integer('age')->nullable();
            $table->string('address')->nullable();
            $table->string('license_image'); // نخزن مسار الصورة
            $table->string('personal_image');
            $table->string('license_category');
            $table->enum('status',['pending','rejected','approved'])
            ->default('pending');
            $table->string('date_of_hire')->nullable();
            $table->text('experience')->nullable(); // تفاصيل الخبرات
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
