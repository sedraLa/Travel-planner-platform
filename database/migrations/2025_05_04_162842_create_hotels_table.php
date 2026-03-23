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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('global_rating')->nullable();
            $table->float('price_per_night');
            $table->integer('total_rooms');
            $table->foreignId('destination_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('cascade');
                   $table->unsignedTinyInteger('stars')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('pets_allowed')->default(false);
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('policies')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('nearby_landmarks')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
