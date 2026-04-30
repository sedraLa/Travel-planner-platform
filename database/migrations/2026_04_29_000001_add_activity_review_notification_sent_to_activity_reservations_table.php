<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_reservations', 'activity_review_notification_sent')) {
                $table->boolean('activity_review_notification_sent')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('activity_reservations', 'activity_review_notification_sent')) {
                $table->dropColumn('activity_review_notification_sent');
            }
        });
    }
};
