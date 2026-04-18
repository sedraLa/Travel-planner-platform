<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'reservation_id')) {
                $table->unsignedBigInteger('reservation_id')->after('reviewable_id');
            }

            $table->unique(['user_id', 'reservation_id'], 'reviews_user_reservation_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_user_reservation_unique');

            if (Schema::hasColumn('reviews', 'reservation_id')) {
                $table->dropColumn('reservation_id');
            }
        });
    }
};
