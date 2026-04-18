<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('reservations', 'hotel_review_notification_sent')) {
                $table->boolean('hotel_review_notification_sent')->default(false)->after('reservation_status');
            }

            if (Schema::hasColumn('reservations', 'review_notified')) {
                $table->dropColumn('review_notified');
            }
        });

        Schema::table('trip_reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('trip_reservations', 'trip_review_notification_sent')) {
                $table->boolean('trip_review_notification_sent')->default(false)->after('status');
            }

            if (! Schema::hasColumn('trip_reservations', 'guide_review_notification_sent')) {
                $table->boolean('guide_review_notification_sent')->default(false)->after('trip_review_notification_sent');
            }

            if (Schema::hasColumn('trip_reservations', 'review_notified')) {
                $table->dropColumn('review_notified');
            }
        });

        Schema::table('transport_reservations', function (Blueprint $table) {
            if (! Schema::hasColumn('transport_reservations', 'driver_review_notification_sent')) {
                $table->boolean('driver_review_notification_sent')->default(false)->after('status');
            }

            if (Schema::hasColumn('transport_reservations', 'review_notified')) {
                $table->dropColumn('review_notified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'hotel_review_notification_sent')) {
                $table->dropColumn('hotel_review_notification_sent');
            }

            if (! Schema::hasColumn('reservations', 'review_notified')) {
                $table->boolean('review_notified')->default(false);
            }
        });

        Schema::table('trip_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('trip_reservations', 'trip_review_notification_sent')) {
                $table->dropColumn('trip_review_notification_sent');
            }

            if (Schema::hasColumn('trip_reservations', 'guide_review_notification_sent')) {
                $table->dropColumn('guide_review_notification_sent');
            }

            if (! Schema::hasColumn('trip_reservations', 'review_notified')) {
                $table->boolean('review_notified')->default(false);
            }
        });

        Schema::table('transport_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('transport_reservations', 'driver_review_notification_sent')) {
                $table->dropColumn('driver_review_notification_sent');
            }

            if (! Schema::hasColumn('transport_reservations', 'review_notified')) {
                $table->boolean('review_notified')->default(false);
            }
        });
    }
};
