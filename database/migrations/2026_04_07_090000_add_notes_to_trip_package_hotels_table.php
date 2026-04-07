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
        Schema::table('trip_package_hotels', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('meal_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_package_hotels', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
