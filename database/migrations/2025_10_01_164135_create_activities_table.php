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
            ]);
            $table->boolean('is_active')->default(true);
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
