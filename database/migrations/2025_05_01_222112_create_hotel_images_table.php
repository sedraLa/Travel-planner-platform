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
        //hhhh
        Schema::create('hotel_images', function (Blueprint $table) {
        $table->id('image_id');
        $table->string('image_url'); 
        $table->boolean('is_primary')->default(false);
        $table->timestamps();
        $table->unsignedBigInteger('hotel_id'); 
        $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_images');
    }
};
