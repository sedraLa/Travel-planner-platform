<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chain_index')->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'status']);
            $table->index(['guide_id', 'status']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('guide_requests');
    }
};
