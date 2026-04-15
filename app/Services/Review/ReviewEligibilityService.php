<?php

namespace App\Services\Review;

class ReviewEligibilityService
{
    public function canReview(User $user, string $type, int $id): bool
    {
        return match ($type) {

            'hotel' => Reservation::where('user_id', $user->id)
                ->where('hotel_id', $id)
                ->where('reservation_status', 'completed')
                ->where('check_out_date', '<', now())
                ->exists(),

            'trip' => TripReservation::where('user_id', $user->id)
                ->where('trip_id', $id)
                ->where('status', 'completed')
                ->whereHas('schedule', fn($q) =>
                    $q->where('end_date', '<', now())
                )
                ->exists(),

            'driver' => TransportReservation::where('user_id', $user->id)
                ->where('driver_id', $id)
                ->where('status', 'completed')
                ->where('dropoff_datetime', '<', now())
                ->exists(),

            'guide' => TripReservation::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereHas('trip', fn($q) =>
                    $q->where('assigned_guide_id', $id)
                )
                ->whereHas('schedule', fn($q) =>
                    $q->where('end_date', '<', now())
                )
                ->exists(),

            default => false,
        };
    }
}