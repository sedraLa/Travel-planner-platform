<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->datetime('pickup_datetime');
            $table->integer('passengers');
            $table->decimal('total_price', 10, 2);
            $table->string('status');
            $table->foreignId('transport_vehicle_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_reservations');
    }
};
