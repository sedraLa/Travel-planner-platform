<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transport_reservations', function (Blueprint $table) {
            $table->string('preferred_category')->nullable()->after('passengers');
            $table->string('preferred_type')->nullable()->after('preferred_category');
        });
    }

    public function down(): void
    {
        Schema::table('transport_reservations', function (Blueprint $table) {
            $table->dropColumn(['preferred_category', 'preferred_type']);
        });
    }
};
