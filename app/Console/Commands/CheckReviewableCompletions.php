<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Reservation;
use App\Models\TripReservation;
use App\Models\TransportReservation;

use App\Events\ReviewableItemCompleted;

class CheckReviewableCompletions extends Command
{
    protected $signature = 'reviews:check-completions';

    protected $description = 'Check completed services and trigger review notifications';

    public function handle()
    {
        $this->checkHotels();
        $this->checkTrips();
        $this->checkDrivers();

        $this->info('Review completion check done.');
    }

    /**
     * HOTELS
     */
    private function checkHotels()
    {
        $reservations = Reservation::where('reservation_status', 'paid')
            ->where('check_out_date', '<', now())
            ->where('hotel_review_notification_sent', false)
            ->get();
            $this->info('Hotels found: ' . $reservations->count());

        foreach ($reservations as $reservation) {

            event(new ReviewableItemCompleted(
                type: 'hotel',
                id: $reservation->hotel_id,
                userId: $reservation->user_id,
                reservationId: $reservation->id
            ));

            $reservation->update([
                'hotel_review_notification_sent' => true
            ]);
        }
    }

    /**
     * TRIPS
     */
    private function checkTrips()
    {
        $tripReservations = TripReservation::where('status', 'completed')
            ->whereHas('schedule', function ($q) {
                $q->where('end_date', '<', now());
            })
            ->where('trip_review_notification_sent', false)
            ->get();
    
        foreach ($tripReservations as $reservation) {
    
            event(new ReviewableItemCompleted(
                type: 'trip',
                id: $reservation->trip_id,
                userId: $reservation->user_id,
                reservationId: $reservation->id
            ));
    
            $reservation->update([
                'trip_review_notification_sent' => true
            ]);
        }

        $guideReservations = TripReservation::where('status', 'completed')
            ->whereHas('schedule', function ($q) {
                $q->where('end_date', '<', now());
            })
            ->where('guide_review_notification_sent', false)
            ->whereHas('trip', fn($q) => $q->whereNotNull('assigned_guide_id'))
            ->with('trip:id,assigned_guide_id')
            ->get();

        foreach ($guideReservations as $reservation) {
            event(new ReviewableItemCompleted(
                type: 'guide',
                id: $reservation->trip->assigned_guide_id,
                userId: $reservation->user_id,
                reservationId: $reservation->id
            ));

            $reservation->update([
                'guide_review_notification_sent' => true,
            ]);
        }
    }
    /**
     * DRIVERS
     */
    private function checkDrivers()
    {
        $reservations = TransportReservation::where('status', 'completed')
            ->where('dropoff_datetime', '<', now())
            ->where('driver_review_notification_sent', false)
            ->get();

        foreach ($reservations as $reservation) {

            event(new ReviewableItemCompleted(
                type: 'driver',
                id: $reservation->driver_id,
                userId: $reservation->user_id,
                reservationId: $reservation->id
            ));

            $reservation->update([
                'driver_review_notification_sent' => true
            ]);
        }
    }
}
