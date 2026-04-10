<?php

namespace App\Services\TripStaffing;

use App\Models\Guide;
use App\Models\GuideAssignment;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GuideAssignmentService
{
    public function rankedGuideIdsForTrip(Trip $trip): array
    {
        $trip->loadMissing(['primaryDestination', 'schedules']);

        [$start, $end] = $this->resolveTripDateRange($trip);
        $destinationTerms = $this->destinationLanguageTerms($trip);

        $availableGuides = $this->availableGuides($start, $end);

        if ($availableGuides->isEmpty()) {
            logger()->info('Guide assignment returned empty result due to hard availability constraints.', [
                'trip_id' => $trip->id,
            ]);

            return [];
        }

        $stageOne = $this->filterByLanguage($availableGuides, $destinationTerms);

        if ($stageOne->isNotEmpty()) {
            logger()->info('Guide assignment stage 1 matched destination language.', [
                'trip_id' => $trip->id,
                'matched_guide_ids' => $stageOne->pluck('id')->all(),
                'language_terms' => $destinationTerms,
            ]);

            return $this->rankFairly($stageOne)->pluck('id')->all();
        }

        logger()->info('Guide assignment stage 1 returned no guides. Falling back to English.', [
            'trip_id' => $trip->id,
        ]);

        $stageTwo = $this->filterByLanguage($availableGuides, ['english']);

        if ($stageTwo->isNotEmpty()) {
            logger()->info('Guide assignment stage 2 matched English fallback.', [
                'trip_id' => $trip->id,
                'matched_guide_ids' => $stageTwo->pluck('id')->all(),
            ]);

            return $this->rankFairly($stageTwo)->pluck('id')->all();
        }

        logger()->info('Guide assignment stage 3 fallback applied (availability only).', [
            'trip_id' => $trip->id,
            'matched_guide_ids' => $availableGuides->pluck('id')->all(),
        ]);

        return $this->rankFairly($availableGuides)->pluck('id')->all();
    }

    private function availableGuides(?Carbon $start, ?Carbon $end): Collection
    {
        return Guide::query()
            ->whereIn('status', ['approved', 'Approved'])
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
                        $assignedStart = Carbon::parse($schedule->start_date)->startOfDay();
                        $assignedEnd = Carbon::parse($schedule->end_date)->endOfDay();

                        if ($assignedStart <= $end && $assignedEnd >= $start) {
                            logger()->info('Guide excluded due to overlapping trip dates.', [
                                'guide_id' => $guide->id,
                                'existing_trip_id' => $assignment->trip_id,
                            ]);

                            return false;
                        }

                        $restWindowStart = $start->copy()->subDays(3);
                        $restWindowEnd = $end->copy()->addDays(3);

                        if ($assignedStart <= $restWindowEnd && $assignedEnd >= $restWindowStart) {
                            logger()->info('Guide excluded due to insufficient 3-day rest period.', [
                                'guide_id' => $guide->id,
                                'existing_trip_id' => $assignment->trip_id,
                            ]);

                            return false;
                        }
                    }
                }

                return true;
            })
            ->values();
    }

    private function filterByLanguage(Collection $guides, array $languageTerms): Collection
    {
        if (empty($languageTerms)) {
            return collect();
        }

        $terms = collect($languageTerms)
            ->map(fn (string $term) => trim(mb_strtolower($term)))
            ->filter()
            ->unique()
            ->values();

        if ($terms->isEmpty()) {
            return collect();
        }

        $languageMatchedIds = Guide::query()
            ->whereIn('id', $guides->pluck('id')->all())
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhereRaw('LOWER(TRIM(languages)) LIKE ?', ['%' . $term . '%']);
                }
            })
            ->pluck('id')
            ->all();

        return $guides->whereIn('id', $languageMatchedIds)->values();
    }

    private function rankFairly(Collection $guides): Collection
    {
        return $guides->sortBy(function (Guide $guide) {
            $lastTripAt = $guide->last_trip_at
                ? Carbon::parse($guide->last_trip_at)->timestamp
                : now()->subYears(20)->timestamp;

            return [
                $lastTripAt,
                (int) ($guide->total_trips_count ?? 0),
            ];
        })->values();
    }

    private function resolveTripDateRange(Trip $trip): array
    {
        $start = $trip->schedules->min('start_date');
        $end = $trip->schedules->max('end_date');

        return [
            $start ? Carbon::parse($start)->startOfDay() : null,
            $end ? Carbon::parse($end)->endOfDay() : null,
        ];
    }

    private function destinationLanguageTerms(Trip $trip): array
    {
        $rawLanguages = (string) optional($trip->primaryDestination)->language;

        return collect(preg_split('/[,\\/|]+/', $rawLanguages) ?: [])
            ->map(fn (string $language) => trim(mb_strtolower($language)))
            ->filter()
            ->values()
            ->all();
    }
}
