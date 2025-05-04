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
        //gggg
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); 
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->foreign('reservation_id')->references('reservation_id')->on('reservations')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users') ->onDelete('set null');
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
