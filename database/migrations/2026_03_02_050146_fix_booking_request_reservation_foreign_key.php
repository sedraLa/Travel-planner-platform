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
        Schema::table('booking_request', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);

            $table->foreign('reservation_id')
                ->references('id')
                ->on('transport_reservations')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_request', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);

            $table->foreign('reservation_id')
                ->references('id')
                ->on('reservations')
                ->cascadeOnDelete();
        });
    }
};
