<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trip_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['trip_id', 'destination_id']);
        });

        // Backfill existing trips with their primary destination.
        DB::table('trips')->select(['id', 'destination_id'])->orderBy('id')->chunkById(500, function ($trips) {
            $now = now();
            $rows = [];

            foreach ($trips as $trip) {
                $rows[] = [
                    'trip_id' => $trip->id,
                    'destination_id' => $trip->destination_id,
                    'sort_order' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($rows)) {
                DB::table('trip_destinations')->insert($rows);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_destinations');
    }
};
