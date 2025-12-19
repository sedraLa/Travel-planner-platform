<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // الوقت والتاريخ
            $table->time('start_time')->nullable()->after('price');
            $table->time('end_time')->nullable()->after('start_time');
            $table->date('start_date')->nullable()->after('end_time');
            $table->date('end_date')->nullable()->after('start_date');

            // التوافر
            $table->string('availability')->after('end_date');

            // معلومات المرشد
            $table->string('guide_name')->nullable()->after('availability');
            $table->string('guide_language')->nullable()->after('guide_name');
            $table->string('contact_number')->nullable()->after('guide_language');

            // المتطلبات
            $table->text('requirements')->nullable()->after('contact_number');

            // مستوى الصعوبة
            $table->enum('difficulty_level', ['easy', 'moderate', 'hard'])->nullable()->after('requirements');

            // المرافق
            $table->json('amenities')->nullable()->after('difficulty_level');

            // العنوان
            $table->string('address')->after('amenities');

            // الحجز مطلوب
            $table->boolean('requires_booking')->default(false)->after('address');


            $table->string('family_friendly')->after('requires_booking');

            // الحيوانات الأليفة
            $table->boolean('pets_allowed')->default(false)->after('family_friendly');

            // المميزات
            $table->text('highlights')->nullable()->after('pets_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn([
                'start_time',
                'end_time',
                'start_date',
                'end_date',
                'availability',
                'guide_name',
                'guide_language',
                'contact_number',
                'requirements',
                'difficulty_level',
                'amenities',
                'address',
                'requires_booking',
                'family_friendly',
                'pets_allowed',
                'highlights'
            ]);
        });
    }
};
