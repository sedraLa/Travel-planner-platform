<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'assigned_driver_id')) {
                $table->dropConstrainedForeignId('assigned_driver_id');
            }

            $columns = [
                'requires_tour_leader',
                'guide_specialization_ids',
                'driver_vehicle_type',
                'driver_vehicle_capacity',
                'driver_trip_type',
                'driver_road_type',
                'ranked_driver_ids',
            ];

            $existingColumns = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('trips', $column)));

            if (! empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            if (! Schema::hasColumn('trips', 'guide_specialization_ids')) {
                $table->json('guide_specialization_ids')->nullable()->after('status');
            }

            if (! Schema::hasColumn('trips', 'requires_tour_leader')) {
                $table->boolean('requires_tour_leader')->default(true)->after('guide_specialization_ids');
            }

            if (! Schema::hasColumn('trips', 'driver_vehicle_type')) {
                $table->string('driver_vehicle_type')->nullable();
            }

            if (! Schema::hasColumn('trips', 'driver_vehicle_capacity')) {
                $table->integer('driver_vehicle_capacity')->nullable();
            }

            if (! Schema::hasColumn('trips', 'driver_trip_type')) {
                $table->string('driver_trip_type')->nullable();
            }

            if (! Schema::hasColumn('trips', 'driver_road_type')) {
                $table->string('driver_road_type')->nullable();
            }

            if (! Schema::hasColumn('trips', 'ranked_driver_ids')) {
                $table->json('ranked_driver_ids')->nullable();
            }

            if (! Schema::hasColumn('trips', 'assigned_driver_id')) {
                $table->foreignId('assigned_driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            }
        });
    }
};
