?php

namespace App\Domain\TransportReservation\Ranking;

use Illuminate\Support\Collection;

class LastTripAndTripsCountStrategy implements DriverRankingStrategy
{
    public function rank(Collection $drivers): Collection
    {
        return $drivers->sortBy([
            fn ($driver) => $driver->last_trip_at ?? now()->subYears(20),
            fn ($driver) => $driver->total_trips_count ?? 0,
        ])->values();
    }
}
