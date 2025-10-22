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
        Schema::table('destinations', function (Blueprint $table) {
         $table->string('timezone')->nullable()->after('description');
        $table->string('language')->nullable()->after('timezone');
        $table->string('currency')->nullable()->after('language');

       
        $table->string('nearest_airport')->nullable()->after('currency');

        
        $table->string('best_time_to_visit')->nullable()->after('nearest_airport');
        $table->string('emergency_numbers')->nullable()->after('best_time_to_visit');
        $table->string('local_tip')->nullable()->after('emergency_numbers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
         $table->dropColumn([
            'timezone',
            'language',
            'currency',
            'nearest_airport',
            'best_time_to_visit',
            'emergency_numbers',
            'local_tip'
        ]);

        });
    }
};
