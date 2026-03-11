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
            $table->decimal('guide_earning', 10, 2)->default(0)->after('ranked_driver_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_reservations', function (Blueprint $table) {
            $table->dropColumn('guide_earning');
        });
    }
};
