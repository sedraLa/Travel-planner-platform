<?php

namespace App\Services\AiTrip\Sanitizers;

class TripPlanSanitizer
{
    public function sanitizeAgainstCatalog(array $plan, array $catalog, int $requestedDuration): array
    {
        //extract allowed data
        $allowedHotelIds = collect($catalog['hotels'])->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedActivityIds = collect($catalog['activities'])->pluck('id')->map(fn ($id) => (int) $id)->all();

        //Days Cleaning
        $sanitizedDays = collect($plan['days'] ?? [])
            ->map(function ($day, $index) use ($allowedHotelIds, $allowedActivityIds) {
                //Activities cleaning
                $activities = collect($day['activities'] ?? [])
                    ->filter(fn ($activity) => in_array((int) ($activity['activity_id'] ?? 0), $allowedActivityIds, true))
                    ->map(fn ($activity) => [
                        //build activities structure
                        'activity_id' => (int) $activity['activity_id'],
                        'start_time' => $activity['start_time'] ?? null,
                        'end_time' => $activity['end_time'] ?? null,
                        'notes' => $activity['notes'] ?? null,
                    ])
                    ->values()
                    ->all();

                return [
                    'day_number' => (int) ($day['day_number'] ?? ($index + 1)), //fallback when no day number
                    'title' => (string) ($day['title'] ?? 'Day ' . ($index + 1)),
                    'description' => (string) ($day['description'] ?? ''),
                    //Hotels cleaning
                    'hotel_id' => in_array((int) ($day['hotel_id'] ?? 0), $allowedHotelIds, true)
                        ? (int) $day['hotel_id']
                        : null,
                    'activities' => $activities,
                ];
            })
            ->values()
            ->all();

         //check number of days = requested duration
        $requestedDuration = max(1, $requestedDuration);
        $plan['days'] = collect(range(1, $requestedDuration))
            ->map(function (int $dayNumber) use ($sanitizedDays) {
                $existing = $sanitizedDays[$dayNumber - 1] ?? null;

                if ($existing) {
                    $existing['day_number'] = $dayNumber;
                    return $existing;
                }

                //A day of duration is missing 
                return [
                    'day_number' => $dayNumber,
                    'title' => 'Day ' . $dayNumber,
                    'description' => '',
                    'hotel_id' => null,
                    'activities' => [],
                ];
            })
            ->all();

        $plan['days'] = $this->optimizeHotelsAcrossDays($plan['days'], $catalog['hotels'] ?? [], $requestedDuration);

        $plan['trip_name'] = (string) ($plan['trip_name'] ?? 'AI Generated Trip');
        $plan['trip_description'] = (string) ($plan['trip_description'] ?? 'A detailed trip crafted with curated local experiences, smart pacing, and practical stay recommendations from your platform catalog.');
        $plan['markdown_summary'] = (string) ($plan['markdown_summary'] ?? '');

        return $plan;
    }

    protected function optimizeHotelsAcrossDays(array $days, array $hotels, int $duration): array
    {
        if (empty($days) || empty($hotels)) {
            return $days;
        }

        $hotelIds = collect($hotels)->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $activeHotelIndex = 0; //current hotel

        foreach ($days as $index => $day) {
            //hotel exist in day
            if (! empty($day['hotel_id']) && in_array((int) $day['hotel_id'], $hotelIds, true)) { 
                $currentIndex = array_search((int) $day['hotel_id'], $hotelIds, true); 
                $activeHotelIndex = $currentIndex === false ? $activeHotelIndex : $currentIndex; //update index
                continue;
            }

            // For long trips (>5 days), rotate hotel every 3 days for better comfort and location fit.
            if ($duration > 5 && $index > 0 && $index % 3 === 0) {
                $activeHotelIndex = ($activeHotelIndex + 1) % count($hotelIds); //got to next hotel & return to first hotel
            }

            $days[$index]['hotel_id'] = $hotelIds[$activeHotelIndex];
        }

        return $days;
    }
}
