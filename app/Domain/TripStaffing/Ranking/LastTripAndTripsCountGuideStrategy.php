<?php

namespace App\Domain\TripStaffing\Ranking;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class LastTripAndTripsCountGuideStrategy implements GuideRankingStrategy
{
    public function rank(Collection $guides): Collection
    {
        return $guides->sortBy(function ($guide) {
            $lastTripAt = $guide->last_trip_at;

            if (! $lastTripAt) {
                $lastTripTimestamp = now()->subYears(20)->timestamp;
            } elseif ($lastTripAt instanceof \DateTimeInterface) {
                $lastTripTimestamp = $lastTripAt->getTimestamp();
            } else {
                $lastTripTimestamp = Carbon::parse($lastTripAt)->timestamp;
            }

            return [
                (int) ($guide->total_trips_count ?? 0),
                $lastTripTimestamp,
            ];
        })->values();
    }
}
