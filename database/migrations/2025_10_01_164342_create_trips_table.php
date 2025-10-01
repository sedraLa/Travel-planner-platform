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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            //destination
            $table->string('destination_name')->nullable();   //custom
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();  //choosen from the system

            // hotel
            $table->string('hotel_name')->nullable();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();

            // trip details
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('travelers_number');
            $table->decimal('budget', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            //booking
            $table->boolean('hotel_booked')->default(false);
            $table->boolean('transport_booked')->default(false);

            //user's flight
            $table->string('flight_number')->nullable();
            $table->string('airline')->nullable();
            $table->string('departure_airport')->nullable();
            $table->string('arrival_airport')->nullable();
            $table->dateTime('departure_time')->nullable();
            $table->dateTime('arrival_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
