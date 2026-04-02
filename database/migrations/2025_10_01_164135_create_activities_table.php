<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Category;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration');
            $table->enum('duration_unit', ['minutes', 'hours', 'days'])->default('hours');
            $table->decimal('price', 8, 2);
            $table->enum('category', [

                Category::CULTURE->value,
                Category::NATURE->value,
                Category::SHOPPING->value,
                Category::SPORTS->value,
                Category::ENTERTAINMENT->value,
                Category::FAMILY->value,
                Category::ROMANCE->value,
                Category::ADVENTURE->value,
                Category::WELLNESS->value,
                Category::FOOD->value,
            ]);
            $table->boolean('is_active')->default(true);
            // الوقت والتاريخ
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // التوافر
            $table->string('availability');

            // معلومات المرشد
            $table->string('guide_name')->nullable();
            $table->string('guide_language')->nullable();
            $table->string('contact_number')->nullable();

            // المتطلبات
            $table->text('requirements')->nullable();

            // مستوى الصعوبة
            $table->enum('difficulty_level', ['easy', 'moderate', 'hard'])->nullable();

            // المرافق
            $table->json('amenities')->nullable();

            // العنوان
            $table->string('address');

            // الحجز مطلوب
            $table->boolean('requires_booking')->default(false);


            $table->string('family_friendly');

            // الحيوانات الأليفة
            $table->boolean('pets_allowed')->default(false);

            // المميزات
            $table->text('highlights')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
