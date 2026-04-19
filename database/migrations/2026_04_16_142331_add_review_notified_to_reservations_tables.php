<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->boolean('review_notified')->default(false);
    });

    Schema::table('trip_reservations', function (Blueprint $table) {
        $table->boolean('review_notified')->default(false);
    });

    Schema::table('transport_reservations', function (Blueprint $table) {
        $table->boolean('review_notified')->default(false);
    });
}

public function down()
{
    Schema::table('reservations', function (Blueprint $table) {
        $table->dropColumn('review_notified');
    });

    Schema::table('trip_reservations', function (Blueprint $table) {
        $table->dropColumn('review_notified');
    });

    Schema::table('transport_reservations', function (Blueprint $table) {
        $table->dropColumn('review_notified');
    });
}
};
