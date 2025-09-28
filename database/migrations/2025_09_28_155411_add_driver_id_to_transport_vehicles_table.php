<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_vehicles', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transport_vehicles', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });
    }
};
