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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('iata_code', 10)->nullable();
            $table->string('city');
            $table->string('country');
            $table->text('description')->nullable();
            $table->text('location_details');
            $table->string('timezone')->nullable();
            $table->string('language')->nullable();
            $table->string('currency')->nullable();
    
           
            $table->string('nearest_airport')->nullable();
    
            
            $table->string('best_time_to_visit')->nullable();
            $table->string('emergency_numbers')->nullable();
            $table->string('local_tip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
