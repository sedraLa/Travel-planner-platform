<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_reservations', function (Blueprint $table) {
            $table->json('ranked_driver_ids')->nullable()->after('driver_status');
            $table->foreignId('transport_vehicle_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transport_reservations', function (Blueprint $table) {
            $table->dropColumn('ranked_driver_ids');
            $table->foreignId('transport_vehicle_id')->nullable(false)->change();
        });
    }
};