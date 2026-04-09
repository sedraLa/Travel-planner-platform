<?php

namespace App\Services\TripStaffing;

use App\Domain\TripStaffing\Ranking\GuideRankingStrategy;
use App\Models\Guide;
use App\Models\Trip;

class GuideRankingService
{
    public function __construct(private GuideRankingStrategy $strategy)
    {
    }

    public function rankedGuideIdsForTrip(Trip $trip): array
    {
        $trip->loadMissing(['primaryDestination', 'schedules']);

        $specializationIds = collect($trip->guide_specialization_ids ?? [])->map(fn ($id) => (int) $id)->filter()->values();
        $city = strtolower((string) optional($trip->primaryDestination)->city);
        $country = strtolower((string) optional($trip->primaryDestination)->country);
        $start = optional($trip->schedules->min('start_date'));
        $end = optional($trip->schedules->max('end_date'));

        $baseQuery = Guide::query()
        ->whereIn('status', ['approved', 'Approved'])
        ->when($specializationIds->isNotEmpty(), function ($query) use ($specializationIds) {
            $query->whereHas('specializations', fn ($spQuery) =>
                $spQuery->whereIn('specializations.id', $specializationIds->all())
            );
        })
        ->when($city || $country, function ($query) use ($city, $country) {
            $query->where(function ($locationQuery) use ($city, $country) {
                if ($city) {
                    $locationQuery->orWhereRaw('LOWER(address) like ?', ["%{$city}%"]);
                }

                if ($country) {
                    $locationQuery->orWhereRaw('LOWER(address) like ?', ["%{$country}%"]);
                }
            });
        })
        ->where(function ($query) {
            $query->whereNull('last_trip_at')
                  ->orWhere('last_trip_at', '<=', now()->subDays(6));
        });

        $guides = (clone $baseQuery)
        ->with(['assignments.trip.schedules'])
        ->get()
        ->filter(function (Guide $guide) use ($start, $end) {
            if (! $start || ! $end) {
                return true;
            }

            foreach ($guide->assignments as $assignment) {
                if (! in_array($assignment->status, ['assigned', 'accepted'], true)) {
                    continue;
                }

                foreach ($assignment->trip?->schedules ?? [] as $schedule) {
                    if ($schedule->start_date <= $end && $schedule->end_date >= $start) {
                        return false;
                    }
                }
            }

            return true;
        })
        ->values();

        if ($trip->requires_tour_leader) {
            $tourLeaders = Guide::query()
                ->whereIn('status', ['approved', 'Approved'])
                ->where('is_tour_leader', true)
                ->when($city || $country, function ($query) use ($city, $country) {
                    $query->where(function ($locationQuery) use ($city, $country) {
                        if ($city) {
                            $locationQuery->orWhereRaw('LOWER(address) like ?', ["%{$city}%"]);
                        }

                        if ($country) {
                            $locationQuery->orWhereRaw('LOWER(address) like ?', ["%{$country}%"]);
                        }
                    });
                })
                ->get();

            $guides = $guides->merge($tourLeaders)->unique('id')->values();
        }

        logger()->info('Trip guide ranking computed', [
            'trip_id' => $trip->id,
            'requires_tour_leader' => (bool) $trip->requires_tour_leader,
            'guide_specialization_ids' => $specializationIds->all(),
            'destination_city' => $city,
            'destination_country' => $country,
            'schedule_start' => $start,
            'schedule_end' => $end,
            'db_matched_count' => (clone $baseQuery)->count(),
            'available_count' => $guides->count(),
            'available_guide_ids' => $guides->pluck('id')->all(),
        ]);

        if ($guides->isEmpty()) {
            return [];
        }

        return $this->strategy->rank($guides)->pluck('id')->all();
    }
}
