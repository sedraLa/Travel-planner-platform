<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //ghghgg
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('hotel_id')->nullable();  
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('set null');
            $table->date('check_in_date');
            $table->date('check_out_date'); 
            $table->integer('rooms_count');
            $table->integer('guest_count');
            $table->float('total_price'); 
            $table->string('reservation_status'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
