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
        Schema::table('trip_reservations', function (Blueprint $table) {
            $table->dropForeign(['day_activity_id']);
            $table->dropColumn('day_activity_id');
            $table->dropForeign(['guide_id']);
            $table->dropColumn('guide_id');

            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_schedule_id')->constrained()->cascadeOnDelete();

            $table->integer('people_count')->default(1);
            $table->decimal('total_price', 10, 2);

            $table->string('status')->default('pending')->change();
            // pending / paid / cancelled
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('trip_reservations', function (Blueprint $table) {

        $table->dropForeign(['trip_id']);
        $table->dropForeign(['trip_package_id']);
        $table->dropForeign(['trip_schedule_id']);

        $table->dropColumn([
            'trip_id',
            'trip_package_id',
            'trip_schedule_id',
            'people_count',
            'total_price'
        ]);


        $table->foreignId('day_activity_id')->constrained()->cascadeOnDelete();
        $table->foreignId('guide_id')->constrained()->cascadeOnDelete();

        $table->string('status')->default('assigned');
    });
}
};
