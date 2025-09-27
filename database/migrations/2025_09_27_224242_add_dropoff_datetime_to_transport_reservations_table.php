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
        Schema::table('transport_reservations', function (Blueprint $table) {
            $table->dateTime('dropoff_datetime')->nullable()->after('pickup_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_reservations', function (Blueprint $table) {
            $table->dropColumn('dropoff_datetime');
        });
    }
};
