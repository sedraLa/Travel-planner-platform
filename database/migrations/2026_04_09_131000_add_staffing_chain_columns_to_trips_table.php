<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->json('ranked_guide_ids')->nullable()->after('requires_tour_leader');
            $table->foreignId('assigned_guide_id')->nullable()->after('ranked_guide_ids')->constrained('guides')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['assigned_guide_id']);
            $table->dropColumn(['ranked_guide_ids', 'assigned_guide_id']);
        });
    }
};
