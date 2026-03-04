<?php


namespace App\Domain\TransportReservation\Ranking;


use Illuminate\Support\Collection;


class LastTripAndTripsCountStrategy implements DriverRankingStrategy
{
    public function rank(Collection $drivers): Collection
    {
        return $drivers->sortBy(function ($driver) {
            return [$driver->total_trips_count ?? 0, $driver->last_trip_at ? $driver->last_trip_at->timestamp : now()->subYears(20)->timestamp];
        })->values();
    }
}