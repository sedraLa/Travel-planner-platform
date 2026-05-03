<?php

namespace App\Services\TripStaffing;

use App\Models\Guide;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GuideRankingService
{
    /**
     * Rank guides to ensure fair distribution of assignments.
     */
    public function rankFairly(Collection $guides): Collection
    {
         // Prioritize guides who haven't worked recently
        return $guides->sortBy(function (Guide $guide) {
            $lastTripAt = $guide->last_trip_at
                ? Carbon::parse($guide->last_trip_at)->timestamp
                : now()->subYears(20)->timestamp; 

                // Secondary factor: total trips handled
            return [
                $lastTripAt,
                (int) ($guide->total_trips_count ?? 0),
            ];
        })->values();
    }
}
