<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->json('guide_specialization_ids')->nullable()->after('status');
            $table->boolean('requires_tour_leader')->default(true)->after('guide_specialization_ids');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'guide_specialization_ids',
                'requires_tour_leader',
            ]);
        });
    }
};
