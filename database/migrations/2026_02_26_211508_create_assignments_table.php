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
        Schema::create('assignments', function (Blueprint $table) {
         $table->id();
         $table->foreignId('transport_vehicles_id') ->constrained() ->cascadeOnDelete();
         $table->foreignId('shift_template_id')->constrained()->cascadeOnDelete();
         $table->unique(['transport_vehicles_id', 'shift_template_id']);
         $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
