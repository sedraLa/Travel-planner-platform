<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('car_model');
            $table->string('plate_number')->unique();
            $table->integer('max_passengers');
            $table->decimal('base_price', 10, 2);
            $table->decimal('price_per_km', 10, 2);
            $table->string('category')->nullable();
            $table->string('type')->nullable();
            $table->string('image');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_vehicles');
    }
};
