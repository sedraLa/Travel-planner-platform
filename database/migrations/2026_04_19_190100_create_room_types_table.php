<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('quantity');
            $table->text('description')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('is_refundable')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
