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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->foreignId('user_id')->constrained()->onDelete('set null');
            $table->foreignId('hotel_id')->constrained()->onDelete('set null');
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
        Schema::dropIfExists('table_reservations');
    }
};
