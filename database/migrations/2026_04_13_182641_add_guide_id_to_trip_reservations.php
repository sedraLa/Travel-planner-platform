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
            if (!Schema::hasColumn('trip_reservations', 'guide_id')) {
                $table->unsignedBigInteger('guide_id');
            }
        
            if (!Schema::hasColumn('trip_reservations', 'guide_paid_at')) {
                $table->timestamp('guide_paid_at')->nullable()->after('guide_earning');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('trip_reservations', function (Blueprint $table) {

        if (Schema::hasColumn('trip_reservations', 'guide_id')) {
     
            try {
                $table->dropForeign(['guide_id']);
            } catch (\Exception $e) {

            }

            $table->dropColumn('guide_id');
        }

        if (Schema::hasColumn('trip_reservations', 'guide_paid_at')) {
            $table->dropColumn('guide_paid_at');
        }
    });
}
};
