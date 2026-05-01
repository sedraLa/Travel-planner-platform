<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropUnique('reviews_user_reservation_unique');
            } catch (\Exception $e) {}
            $table->unique(
                ['user_id', 'reservation_id', 'reviewable_type', 'reviewable_id'],
                'reviews_full_unique'
            );
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            try {
                $table->dropUnique('reviews_full_unique');
            } catch (\Exception $e) {}
            $table->unique(
                ['user_id', 'reservation_id'],
                'reviews_user_reservation_unique'
            );
        });
    }
};