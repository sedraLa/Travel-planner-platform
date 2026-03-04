<?php


namespace App\Domain\TransportReservation\Ranking;


use Illuminate\Support\Collection;
use Carbon\Carbon;


class LastTripAndTripsCountStrategy implements DriverRankingStrategy
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

            return [
                (int) ($driver->total_trips_count ?? 0),
                $lastTripTimestamp,
            ];
        })->values();
    }
}