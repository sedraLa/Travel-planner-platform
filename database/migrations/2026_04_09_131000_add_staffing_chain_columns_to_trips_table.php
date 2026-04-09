<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->json('ranked_guide_ids')->nullable()->after('driver_road_type');
            $table->json('ranked_driver_ids')->nullable()->after('ranked_guide_ids');
            $table->foreignId('assigned_guide_id')->nullable()->after('ranked_driver_ids')->constrained('guides')->nullOnDelete();
            $table->foreignId('assigned_driver_id')->nullable()->after('assigned_guide_id')->constrained('drivers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['assigned_guide_id']);
            $table->dropForeign(['assigned_driver_id']);
            $table->dropColumn(['ranked_guide_ids', 'ranked_driver_ids', 'assigned_guide_id', 'assigned_driver_id']);
        });
    }
};
