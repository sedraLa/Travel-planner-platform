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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->integer('api_reference_id'); // API reference ID
            $table->integer('available_seats');  
            $table->string('airline');
            $table->integer('flight_number')->unique(); 
            $table->string('departure_airport');
            $table->string('arrival_airport'); 
            $table->foreignId('destination_id')->constrained()->onDelete('set null');
            $table->dateTime('departure_time'); 
            $table->dateTime('arrival_time');
            $table->string('booking_url'); 
            $table->string('duration');
            $table->float('price');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_flights');
    }
};
