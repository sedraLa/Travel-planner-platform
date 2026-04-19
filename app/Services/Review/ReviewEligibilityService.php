<?php

namespace App\Services\Review;
use App\Models\User;
use App\Models\Reservation;
use App\Models\TripReservation;
use App\Models\TransportReservation;
use Illuminate\Database\Eloquent\Model;


class ReviewEligibilityService
{
    public function canReview(User $user, string $type, int $id, ?int $reservationId = null): bool
    {
        if ($reservationId !== null) {
            return $this->resolveOwnedReservation($user, $type, $id, $reservationId) !== null;
        }

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
                ->where('status', 'completed')
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
                ->where('status', 'completed')
                ->whereHas('trip', fn($q) => $q->where('assigned_guide_id', $reviewableId))
                ->whereHas('schedule', fn($q) => $q->where('end_date', '<', now()))
                ->first(),

            default => null,
        };
    }
}
