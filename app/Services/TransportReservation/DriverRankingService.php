<?php

namespace App\Services\TransportReservation;

use App\Domain\TransportReservation\Ranking\DriverRankingStrategy;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\TransportReservation;
use Carbon\CarbonInterface;
use Carbon\Carbon;

class DriverRankingService
{
    public function __construct(private DriverRankingStrategy $strategy)
    {
    }

    public function rankedDriverIdsForReservation(TransportReservation $reservation): array
    {
        $pickup = $reservation->pickup_datetime;
        $dropoff = $reservation->dropoff_datetime ?? $pickup->copy()->addHours(2);
        $shortDay = strtolower($pickup->format("D")); // mon
        $fullDay = strtolower($pickup->format("l")); // monday
        $requestTime = $pickup->format("H:i:s");

        $assignmentsQuery = Assignment::query()
            ->whereHas("driver", fn ($driverQuery) => $driverQuery->whereIn("status", ["approved", "Approved"])) // Filter by driver status
            ->whereNotNull("transport_vehicle_id"); // Ensure assignment has a vehicle

        // Apply vehicle type and category filtering directly to the assignments query
        if (!empty($reservation->preferred_category)) {
            $assignmentsQuery->whereHas("vehicle", fn ($vehicleQuery) => 
                $vehicleQuery->whereRaw("LOWER(category) = ?", [strtolower($reservation->preferred_category)]) //to pass case sensitive
            );
        }

        if (!empty($reservation->preferred_type)) {
            $assignmentsQuery->whereHas("vehicle", fn ($vehicleQuery) => 
                $vehicleQuery->whereRaw("LOWER(type) = ?", [strtolower($reservation->preferred_type)])
            );
        }

        //max passengers filtering
        $assignments = $assignmentsQuery
            ->whereHas("vehicle", function ($vehicleQuery) use ($reservation) {
                $vehicleQuery->where("max_passengers", ">=", $reservation->passengers);
            })
            ->with(["driver", "shiftTemplate", "vehicle"])
            ->get();

        $driverIds = $assignments
            ->filter(function ($assignment) use ($shortDay, $fullDay, $requestTime, $pickup, $dropoff, $reservation) {
                if (!$assignment->driver || !$assignment->shiftTemplate) {
                    return false;
                }

                //transform days to lower case collection
                $days = collect($assignment->shiftTemplate->days_of_week ?? [])
                    ->map(fn ($day) => strtolower((string) $day))
                    ->values();

                    //check of driver works these days
                $dayMatches = $days->contains($shortDay) || $days->contains($fullDay);

                if (!$dayMatches) {
                    return false;
                }

                //time check
                if (!$this->isWithinShift($requestTime, $assignment->shiftTemplate->start_time, $assignment->shiftTemplate->end_time)) {
                    return false;
                }

                //overlapped reservations check
                return !$this->hasVehicleOverlap(
                    vehicleId: $assignment->transport_vehicle_id,
                    pickup: $pickup,
                    dropoff: $dropoff,
                    reservationIdToIgnore: $reservation->id,
                );
            })
            ->pluck("driver.id")
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($driverIds)) {
            return [];
        }

        $drivers = Driver::query()->with('user:id,country')->whereIn("id", $driverIds)->get();

        $pickupLocationParts = explode(',', (string) $reservation->pickup_location);
        $pickupCountry = trim((string) end($pickupLocationParts));

        if ($pickupCountry !== '') {
            $drivers = $drivers->filter(function ($driver) use ($pickupCountry) {
                return $driver->user?->country === $pickupCountry;
            })->values();
        }

        if ($drivers->isEmpty()) {
            return [];
        }

        //ranking strategy
        return $this->strategy->rank($drivers)->pluck("id")->all();
    }

    private function isWithinShift(string $requestTime, string $startTime, string $endTime): bool
    {
        $request = Carbon::createFromFormat("H:i:s", $requestTime);
        $start = Carbon::createFromFormat("H:i:s", $startTime);
        $end = Carbon::createFromFormat("H:i:s", $endTime);

        // Normal shift: 09:00 -> 17:00 , 14 between ?
        if ($start->lte($end)) {
            return $request->betweenIncluded($start, $end);
        }

        // Overnight shift: 22:00 -> 06:00 , 1 is between?
        return $request->gte($start) || $request->lte($end); //after 22 or before 6 is included in work schedule
    }

    private function hasVehicleOverlap(int $vehicleId, CarbonInterface $pickup, CarbonInterface $dropoff, ?int $reservationIdToIgnore = null): bool
    {
        //get all vehicle reservations
        return TransportReservation::query()
            ->where("transport_vehicle_id", $vehicleId)
            ->when($reservationIdToIgnore, fn ($query) => $query->where("id", "!=", $reservationIdToIgnore))
            ->where("status", "confirmed")
            ->where("driver_status","pending")
            ->where(function ($query) use ($pickup, $dropoff) { //check time overlap
                $query
                    ->whereBetween("pickup_datetime", [$pickup, $dropoff])
                    ->orWhereBetween("dropoff_datetime", [$pickup, $dropoff])
                    ->orWhere(function ($inner) use ($pickup, $dropoff) {
                        $inner->where("pickup_datetime", "<=", $pickup) 
                            ->where("dropoff_datetime", ">=", $dropoff); 
                    });
            })
            ->exists();
    }
}
