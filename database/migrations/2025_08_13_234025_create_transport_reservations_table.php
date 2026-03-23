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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->datetime('pickup_datetime');
            $table->dateTime('dropoff_datetime')->nullable();
            $table->integer('passengers');
            $table->decimal('total_price', 10, 2);
            $table->string('status')->default('completed');
            $table->foreignId('transport_vehicle_id')->constrained()->onDelete('cascade');
            $table->string('preferred_category')->nullable();
            $table->string('preferred_type')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_reservations');
    }
};
