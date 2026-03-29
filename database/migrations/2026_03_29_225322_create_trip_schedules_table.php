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
        Schema::create('trip_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId("trip_id")->constrained()->cascadeOnDelete();
            $table->date("start_date");
            $table->date("end_date");
            $table->date("booking_deadline")->nullable();
            $table->integer("available_seats")->nullable();
            $table->decimal("price_modifier", 5, 2)->default(0); // to edit the price of the package for a specific season without changing the standard price
            $table->string("status")->default("available"); //status of available seats
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_schedules');
    }
};
