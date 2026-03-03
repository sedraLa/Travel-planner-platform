<?php

namespace App\Services\TransportReservation;

use App\Domain\TransportReservation\Ranking\DriverRankingStrategy;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\TransportReservation;
use Carbon\Carbon;

class DriverRankingService
{
    public function __construct(private DriverRankingStrategy $strategy)
    {
    }

    public function rankedDriverIdsForReservation(TransportReservation $reservation): array
    {
        $pickup = $reservation->pickup_datetime;
        $shortDay = strtolower($pickup->format('D')); // mon
        $fullDay = strtolower($pickup->format('l')); // monday
        $requestTime = $pickup->format('H:i:s');

        $assignments = Assignment::query()
            ->whereHas('driver', fn ($driverQuery) => $driverQuery->whereIn('status', ['approved', 'Approved']))
            ->with(['driver', 'shiftTemplate'])
            ->get();

        $driverIds = $assignments
            ->filter(function ($assignment) use ($shortDay, $fullDay, $requestTime) {
                if (!$assignment->driver || !$assignment->shiftTemplate) {
                    return false;
                }

                $days = collect($assignment->shiftTemplate->days_of_week ?? [])
                    ->map(fn ($day) => strtolower((string) $day))
                    ->values();

                $dayMatches = $days->contains($shortDay) || $days->contains($fullDay);

                if (!$dayMatches) {
                    return false;
                }

                return $this->isWithinShift($requestTime, $assignment->shiftTemplate->start_time, $assignment->shiftTemplate->end_time);
            })
            ->pluck('driver.id')
            ->filter()
            ->unique()
            ->values()
            ->all();
            
            if (empty($driverIds)) {
                // Fallback: if no shift assignment matches, still try approved drivers
                // so booking requests can continue instead of immediate cancellation.
                $driverIds = Driver::query()
                    ->whereIn('status', ['approved', 'Approved'])
                    ->pluck('id')
                    ->all();
            }

        if (empty($driverIds)) {
            // Fallback: if no shift assignment matches, still try approved drivers
            // so booking requests can continue instead of immediate cancellation.
            $driverIds = Driver::query()
                ->whereIn('status', ['approved', 'Approved'])
                ->pluck('id')
                ->all();
        }

        $drivers = Driver::query()->whereIn('id', $driverIds)->get();

        return $this->strategy->rank($drivers)->pluck('id')->all();
    }

    private function isWithinShift(string $requestTime, string $startTime, string $endTime): bool
    {
        $request = Carbon::createFromFormat('H:i:s', $requestTime);
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);

        // Normal shift: 09:00 -> 17:00
        if ($start->lte($end)) {
            return $request->betweenIncluded($start, $end);
        }

        // Overnight shift: 22:00 -> 06:00
        return $request->gte($start) || $request->lte($end);
    }
}
