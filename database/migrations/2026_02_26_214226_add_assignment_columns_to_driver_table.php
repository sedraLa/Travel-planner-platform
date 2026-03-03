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
        Schema::table('drivers', function (Blueprint $table) {
          
            $table->dateTime('last_trip_at')->nullable()->after('date_of_hire');
            $table->integer('total_trips_count')->default(0)->after('last_trip_at');
            $table->decimal('earnings_balance', 10, 2)->default(0)->after('total_trips_count');



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
             $table->dropForeign(['assignment_id']);
             $table->dropColumn(['assignment_id', 'last_trip_at', 'total_trips_count', 'earnings_balance']);
        });
    }
};
