<?php

namespace App\Services\TripStaffing;

use App\Domain\TransportReservation\Ranking\DriverRankingStrategy;
use App\Models\Driver;
use App\Models\Trip;

class DriverRankingService
{
    public function __construct(private DriverRankingStrategy $strategy)
    {
    }

    public function rankedDriverIdsForTrip(Trip $trip): array
    {
        $trip->loadMissing(['primaryDestination', 'schedules']);

        $city = strtolower((string) optional($trip->primaryDestination)->city);
        $country = strtolower((string) optional($trip->primaryDestination)->country);
        $start = optional($trip->schedules->min('start_date'));
        $end = optional($trip->schedules->max('end_date'));

        $drivers = Driver::query()
            ->whereIn('status', ['approved', 'Approved'])
            ->whereHas('assignment', function ($assignmentQuery) use ($trip) {
                $assignmentQuery->whereHas('vehicle', function ($vehicleQuery) use ($trip) {
                    if (! empty($trip->driver_vehicle_type)) {
                        $vehicleQuery->whereRaw('LOWER(type) = ?', [strtolower((string) $trip->driver_vehicle_type)]);
                    }

                    if (! empty($trip->driver_vehicle_capacity)) {
                        $vehicleQuery->where('max_passengers', '>=', (int) $trip->driver_vehicle_capacity);
                    }
                });
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
            ->with(['tripTransports.trip.schedules', 'reservations'])
            ->get()
            ->filter(function (Driver $driver) use ($start, $end) {
                if (! $start || ! $end) {
                    return true;
                }

                foreach ($driver->tripTransports as $transport) {
                    foreach ($transport->trip?->schedules ?? [] as $schedule) {
                        if ($schedule->start_date <= $end && $schedule->end_date >= $start) {
                            return false;
                        }
                    }
                }

                foreach ($driver->reservations as $reservation) {
                    if (! in_array($reservation->status, ['driver_assigned', 'confirmed'], true)) {
                        continue;
                    }

                    $pickup = optional($reservation->pickup_datetime)?->toDateString();
                    $dropoff = optional($reservation->dropoff_datetime)?->toDateString() ?? $pickup;

                    if ($pickup && $dropoff && $pickup <= $end && $dropoff >= $start) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

        if ($drivers->isEmpty()) {
            return [];
        }

        return $this->strategy->rank($drivers)->pluck('id')->all();
    }
}
