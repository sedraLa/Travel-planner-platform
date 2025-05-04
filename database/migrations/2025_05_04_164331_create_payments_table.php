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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); 
            $table->foreignId('reservation_id')->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('set null');
            $table->float('amount');
            $table->string('status');
            $table->unsignedBigInteger('transaction_id');
            $table->date('payment_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
