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
            $table->foreignId('transport_id')->constrained()->onDelete('cascade');
            $table->string('car_model');
            $table->string('plate_number')->unique();
            $table->string('driver_name');
            $table->string('driver_contact');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_vehicles');
    }
};
