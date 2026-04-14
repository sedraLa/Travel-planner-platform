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
        Schema::table('trip_reservations', function (Blueprint $table) {
           $table->foreignId('guide_id')->constrained()->nullOnDelete()->after('trip_id');
           $table->timestamp('guide_paid_at')->nullable()->after('guide_earning');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_reservations', function (Blueprint $table) {
            
        $table->dropForeign(['guide_id']);
        $table->dropColumn('guide_id');
        $table->dropColumn('guide_paid_at');
        });
    }
};
