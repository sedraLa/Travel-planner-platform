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
        $reservations = Reservation::where('reservation_status', 'completed')
            ->where('check_out_date', '<', now())
            ->where('review_notified', false)
            ->get();

        foreach ($reservations as $reservation) {

            event(new ReviewableItemCompleted(
                type: 'hotel',
                id: $reservation->hotel_id,
                userId: $reservation->user_id
            ));

            $reservation->update([
                'review_notified' => true
            ]);
        }
    }

    /**
     * TRIPS
     */
    private function checkTrips()
    {
        $reservations = TripReservation::where('status', 'completed')
            ->whereHas('schedule', function ($q) {
                $q->where('end_date', '<', now());
            })
            ->where('review_notified', false)
            ->get();
    
        foreach ($reservations as $reservation) {
    
            // 1. Trip review
            event(new ReviewableItemCompleted(
                type: 'trip',
                id: $reservation->trip_id,
                userId: $reservation->user_id
            ));
    
            // 2. Guide review 
            if ($reservation->trip && $reservation->trip->assigned_guide_id) {
                event(new ReviewableItemCompleted(
                    type: 'guide',
                    id: $reservation->trip->assigned_guide_id,
                    userId: $reservation->user_id
                ));
            }
    
            $reservation->update([
                'review_notified' => true
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
            ->where('review_notified', false)
            ->get();

        foreach ($reservations as $reservation) {

            event(new ReviewableItemCompleted(
                type: 'driver',
                id: $reservation->driver_id,
                userId: $reservation->user_id
            ));

            $reservation->update([
                'review_notified' => true
            ]);
        }
    }
}