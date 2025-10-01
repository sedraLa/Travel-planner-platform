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
        Schema::table('hotels', function (Blueprint $table) {
            $table->unsignedTinyInteger('stars')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('pets_allowed')->default(false);
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->text('policies')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('nearby_landmarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['stars','amenities','pets_allowed','check_in_time','check_out_time','policies','phone_number','email'
            ,'website','nearby_landmarks']);
        });
    }
};
