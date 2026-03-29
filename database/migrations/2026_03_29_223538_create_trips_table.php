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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId("destination_id")->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string("slug")->unique();
            $table->integer("duration_days");
            $table->string("category")->nullable();

            $table->integer("max_participants")->nullable();

            $table->text("meeting_point_description")->nullable();
            $table->string("meeting_point_address")->nullable();

            $table->boolean("is_ai_generated")->default(false);
            $table->text("ai_prompt")->nullable();

            $table->string("status")->default("draft");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
