<?php

namespace App\Services\Review;

use App\Models\User;
use App\Models\Reservation;
use App\Models\TripReservation;
use App\Models\TransportReservation;
use App\Models\ActivityReservation;
use Illuminate\Database\Eloquent\Model;

class ReviewEligibilityService
{
    //get user reservation (source of truth)
    public function resolveOwnedReservation(User $user, string $type, int $reviewableId, int $reservationId): ?Model
    {
        return match ($type) {
            'hotel' => Reservation::whereKey($reservationId)
                ->where('user_id', $user->id)
                ->where('hotel_id', $reviewableId)
                ->where('reservation_status', 'paid')
                ->where('check_out_date', '<', now())
                ->first(),

            'trip' => TripReservation::whereKey($reservationId)
                ->where('user_id', $user->id)
                ->where('trip_id', $reviewableId)
                ->where('status', 'paid')
                ->whereHas('schedule', fn($q) => $q->where('end_date', '<', now()))
                ->first(),

            'driver' => TransportReservation::whereKey($reservationId)
                ->where('user_id', $user->id)
                ->where('driver_id', $reviewableId)
                ->where('status', 'completed')
                ->where('dropoff_datetime', '<', now())
                ->first(),

            'guide' => TripReservation::whereKey($reservationId)
                ->where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereHas('trip', fn($q) => $q->where('assigned_guide_id', $reviewableId))
                ->whereHas('schedule', fn($q) => $q->where('end_date', '<', now()))
                ->first(),

            'activity' => ActivityReservation::whereKey($reservationId)
                ->where('user_id', $user->id)
                ->where('activity_id', $reviewableId)
                ->where('status', 'paid')
                ->where('activity_date', '<', now())
                ->first(),

            default => null,
        };
    }
}