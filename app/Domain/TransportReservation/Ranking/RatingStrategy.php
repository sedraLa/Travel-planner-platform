<?php

namespace App\Domain\TransportReservation\Ranking;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class RatingStrategy implements DriverRankingStrategy
{
    public function rank(Collection $drivers): Collection
    {
        return $drivers->sortByDesc(function ($driver) {

            $lastTripAt = $driver->last_trip_at;

            if (!$lastTripAt) {
                $lastTripTimestamp = now()->subYears(20)->timestamp;
            } elseif ($lastTripAt instanceof \DateTimeInterface) {
                $lastTripTimestamp = $lastTripAt->getTimestamp();
            } else {
                $lastTripTimestamp = Carbon::parse($lastTripAt)->timestamp;
            }

            // rating comes from reviews() accessor
            $averageRating = $driver->average_rating;

            // check if driver actually has reviews
            $hasRating = $driver->reviews()->exists();

            return [
                $hasRating ? 1 : 0,
                $hasRating ? (float) $averageRating : 0,
                (int) ($driver->total_trips_count ?? 0),
                $lastTripTimestamp,
            ];

        })->values();
    }
}