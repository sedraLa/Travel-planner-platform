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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('languages')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->string('certificate_image')->nullable();
            $table->string('status')->nullable();
            $table->decimal('earnings_balance', 10, 2)->default(0);
            $table->string('personal_image');
            $table->integer('age')->nullable();
            $table->string('address')->nullable();
            $table->string('date_of_hire')->nullable();
            $table->integer('total_trips_count')->default(0);
            $table->dateTime('last_trip_at')->nullable();
            $table->boolean('is_tour_leader')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
