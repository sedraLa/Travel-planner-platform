<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('room_type_id')->nullable()->after('hotel_id')->constrained('room_types')->nullOnDelete();
            $table->date('check_in')->nullable()->after('check_out_date');
            $table->date('check_out')->nullable()->after('check_in');
            $table->unsignedInteger('guests')->nullable()->after('guest_count');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['room_type_id']);
            $table->dropColumn(['room_type_id', 'check_in', 'check_out', 'guests']);
        });
    }
};
