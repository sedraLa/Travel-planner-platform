<?php

namespace App\Services\TripStaffing;

use App\Models\Guide;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GuideAvailabilityService
{
    /**
     * Retrieve guides eligible for a trip based on location and availability constraints.
     */
    public function availableGuides(?Carbon $start, ?Carbon $end, ?string $destinationCity, ?string $destinationCountry): Collection
    {
        if (! $destinationCountry && ! $destinationCity) {
            logger()->warning('Guide assignment stopped: destination location is missing.', []);

            return collect();
        }

        $baseQuery = Guide::query()
            ->whereIn('status', ['approved', 'Approved'])
            ->with(['assignments.trip.schedules'])
            ->where(function ($query) use ($destinationCity, $destinationCountry) {
                if ($destinationCountry) {
                    $query->whereRaw('LOWER(TRIM(address)) LIKE ?', ['%' . $destinationCountry . '%']);
                }

                if ($destinationCity) {
                    $query->whereRaw('LOWER(TRIM(address)) LIKE ?', ['%' . $destinationCity . '%']);
                }
            });

        return $baseQuery
            ->get()
            ->filter(function (Guide $guide) use ($start, $end) {
                if (! $start || ! $end) {
                    return true;
                }

                foreach ($guide->assignments as $assignment) {
                    // Only consider active assignments
                    if (! in_array($assignment->status, ['assigned', 'accepted'], true)) {
                        continue;
                    }

                    foreach ($assignment->trip?->schedules ?? [] as $schedule) {
                        $assignedStart = Carbon::parse($schedule->start_date)->startOfDay();
                        $assignedEnd = Carbon::parse($schedule->end_date)->endOfDay();

                        // Exclude if schedules overlap
                        if ($assignedStart <= $end && $assignedEnd >= $start) {
                            logger()->info('Guide excluded due to overlapping trip dates.', [
                                'guide_id' => $guide->id,
                                'existing_trip_id' => $assignment->trip_id,
                            ]);

                            return false;
                        }

                        //break days
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
}
