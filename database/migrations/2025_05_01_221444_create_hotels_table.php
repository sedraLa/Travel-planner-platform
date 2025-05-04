<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    //njnjjnnj
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
        $table->string('city')();
        $table->string('country');
        $table->string('global_rating')->nullable();
        $table->float('price_per_night');
        $table->integer('total_rooms');
        $table->unsignedBigInteger('destination_id')->nullable();
        $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('set null');
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
