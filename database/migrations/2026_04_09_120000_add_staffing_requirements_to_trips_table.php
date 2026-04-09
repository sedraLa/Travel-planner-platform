<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->json('guide_specialization_ids')->nullable()->after('status');
            $table->boolean('requires_tour_leader')->default(true)->after('guide_specialization_ids');
            $table->string('driver_vehicle_type')->nullable()->after('requires_tour_leader');
            $table->unsignedInteger('driver_vehicle_capacity')->nullable()->after('driver_vehicle_type');
            $table->string('driver_trip_type')->nullable()->after('driver_vehicle_capacity');
            $table->string('driver_road_type')->nullable()->after('driver_trip_type');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'guide_specialization_ids',
                'requires_tour_leader',
                'driver_vehicle_type',
                'driver_vehicle_capacity',
                'driver_trip_type',
                'driver_road_type',
            ]);
        });
    }
};
