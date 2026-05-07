<?php

namespace App\Domain\TransportReservation\Ranking;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class RatingStrategy implements DriverRankingStrategy
{
    public function rank(Collection $drivers): Collection
    {
        return $drivers->sortBy(function ($driver) {
            $lastTripAt = $driver->last_trip_at;

            if (!$lastTripAt) {
                $lastTripTimestamp = now()->subYears(20)->timestamp;
            } elseif ($lastTripAt instanceof \DateTimeInterface) {
                $lastTripTimestamp = $lastTripAt->getTimestamp();
            } else {
                $lastTripTimestamp = Carbon::parse($lastTripAt)->timestamp;
            }

            $averageRating = $driver->average_rating;
            $hasRating = $averageRating !== null;

            return [
                $hasRating ? 0 : 1,
                $hasRating ? -1 * (float) $averageRating : 0,
                (int) ($driver->total_trips_count ?? 0),
                $lastTripTimestamp,
            ];
        })->values();
    }
}
