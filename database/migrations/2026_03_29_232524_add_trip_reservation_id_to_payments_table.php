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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('trip_reservation_id')
            ->nullable()
            ->constrained('trip_reservations')
            ->onDelete('set null');
        });
    }

    public function down(): void
    { Schema::table('payments', function (Blueprint $table) {
        $table->dropForeign(['trip_reservation_id']);
        $table->dropColumn('trip_reservation_id');
    });
    }
};
