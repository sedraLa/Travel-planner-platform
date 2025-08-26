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
        Schema::table('transport_vehicles', function (Blueprint $table) {
            $table->integer('max_passengers')->after('driver_contact');
            $table->decimal('base_price', 10, 2)->after('max_passengers');
            $table->decimal('price_per_km', 10, 2)->after('base_price');
            $table->string('category')->nullable()->after('price_per_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_vehicles', function (Blueprint $table) {
            $table->dropColumn(['max_passengers','base_price','price_per_km','category']);
        });
    }
};
