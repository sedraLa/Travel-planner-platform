<?php

namespace App\Services\TripStaffing;

use App\Models\Trip;
use Carbon\Carbon;

class GuideAssignmentService
{
    public function __construct(
        private GuideAvailabilityService $guideAvailabilityService,
        private GuideLanguageFilterService $guideLanguageFilterService,
        private GuideRankingService $guideRankingService,
    ) {
    }

    public function rankedGuideIdsForTrip(Trip $trip): array
    {
        $trip->loadMissing(['primaryDestination', 'schedules']);

        [$start, $end] = $this->resolveTripDateRange($trip);
        [$destinationCity, $destinationCountry] = $this->destinationLocation($trip);
        $destinationTerms = $this->destinationLanguageTerms($trip); //get destination local language

        $availableGuides = $this->guideAvailabilityService->availableGuides($start, $end, $destinationCity, $destinationCountry);

        if ($availableGuides->isEmpty()) {
            logger()->info('Guide assignment returned empty result due to hard availability constraints.', [
                'trip_id' => $trip->id,
            ]);

            return [];
        }

          // match destination language
        $stageOne = $this->guideLanguageFilterService->filterByLanguage($availableGuides, $destinationTerms);

        if ($stageOne->isNotEmpty()) {
            logger()->info('Guide assignment stage 1 matched destination language.', [
                'trip_id' => $trip->id,
                'matched_guide_ids' => $stageOne->pluck('id')->all(),
                'language_terms' => $destinationTerms,
            ]);

            return $this->guideRankingService->rankFairly($stageOne)->pluck('id')->all();
        }

        // fallback to English
        logger()->info('Guide assignment stage 1 returned no guides. Falling back to English.', [
            'trip_id' => $trip->id,
        ]);

        $stageTwo = $this->guideLanguageFilterService->filterByLanguage($availableGuides, ['english']);

        if ($stageTwo->isNotEmpty()) {
            logger()->info('Guide assignment stage 2 matched English fallback.', [
                'trip_id' => $trip->id,
                'matched_guide_ids' => $stageTwo->pluck('id')->all(),
            ]);

            return $this->guideRankingService->rankFairly($stageTwo)->pluck('id')->all();
        }

        // Final fallback: use all available guides
        logger()->info('Guide assignment stage 3 fallback applied (availability only).', [
            'trip_id' => $trip->id,
            'matched_guide_ids' => $availableGuides->pluck('id')->all(),
        ]);

        return $this->guideRankingService->rankFairly($availableGuides)->pluck('id')->all();
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

        //cleaning languages
        return collect(preg_split('/[,\\/|]+/', $rawLanguages) ?: [])
            ->map(fn (string $language) => trim(mb_strtolower($language)))
            ->filter()
            ->values()
            ->all();
    }

    private function destinationLocation(Trip $trip): array
    {
        return [
            $this->normalized(optional($trip->primaryDestination)->city),
            $this->normalized(optional($trip->primaryDestination)->country),
        ];
    }

    //cleaning
    private function normalized(?string $value): ?string
    {
        $normalized = trim(mb_strtolower((string) $value));

        return $normalized === '' ? null : $normalized;
    }
}
