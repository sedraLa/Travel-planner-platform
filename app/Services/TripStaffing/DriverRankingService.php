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
        $end   = optional($trip->schedules->max('start_date'));

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        */

        $baseQuery = Driver::query()
            ->whereIn('status', ['approved', 'Approved'])

            // لازم يكون عنده assignment فعلاً
            ->whereHas('assignment', function ($assignmentQuery) use ($trip) {

                // لازم يكون في vehicle مربوط
                $assignmentQuery->whereNotNull('transport_vehicle_id');

                $assignmentQuery->whereHas('vehicle', function ($vehicleQuery) use ($trip) {

                    if (!empty($trip->driver_vehicle_type)) {
                        $vehicleQuery->whereRaw(
                            'LOWER(type) = ?',
                            [strtolower($trip->driver_vehicle_type)]
                        );
                    }

                    if (!empty($trip->driver_vehicle_capacity)) {
                        $vehicleQuery->where(
                            'max_passengers',
                            '>=',
                            (int) $trip->driver_vehicle_capacity
                        );
                    }
                });
            })

            // location filter (خفيف وما بيكسر الداتا)
            ->when($city || $country, function ($query) use ($city, $country) {
                $query->where(function ($q) use ($city, $country) {

                    if ($city) {
                        $q->orWhereRaw('LOWER(address) LIKE ?', ["%{$city}%"]);
                    }

                    if ($country) {
                        $q->orWhereRaw('LOWER(address) LIKE ?', ["%{$country}%"]);
                    }
                });
            });

        /*
        |--------------------------------------------------------------------------
        | DEBUG (مهم جدًا)
        |--------------------------------------------------------------------------
        */

        logger()->info('Driver ranking debug', [
            'total_drivers' => Driver::count(),
            'drivers_with_assignment' => Driver::whereHas('assignment')->count(),
            'drivers_with_vehicle' => Driver::whereHas('assignment.vehicle')->count(),
            'base_query_count' => (clone $baseQuery)->count(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | FETCH + FILTER AVAILABILITY
        |--------------------------------------------------------------------------
        */

        $drivers = (clone $baseQuery)
            ->with(['tripTransports.trip.schedules', 'reservations'])
            ->get()
            ->filter(function (Driver $driver) use ($start, $end) {

                // إذا ما في schedule ما منمنع
                if (! $start || ! $end) {
                    return true;
                }

                // تضارب مع trips
                foreach ($driver->tripTransports as $transport) {
                    foreach ($transport->trip?->schedules ?? [] as $schedule) {
                        if ($schedule->start_date <= $end && $schedule->end_date >= $start) {
                            return false;
                        }
                    }
                }

                // تضارب مع reservations
                foreach ($driver->reservations as $reservation) {

                    if (!in_array($reservation->status, ['driver_assigned', 'confirmed'], true)) {
                        continue;
                    }

                    $pickup  = optional($reservation->pickup_datetime)?->toDateString();
                    $dropoff = optional($reservation->dropoff_datetime)?->toDateString() ?? $pickup;

                    if ($pickup && $dropoff && $pickup <= $end && $dropoff >= $start) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | FINAL LOG
        |--------------------------------------------------------------------------
        */

        logger()->info('Trip driver ranking computed', [
            'trip_id' => $trip->id,
            'available_count' => $drivers->count(),
            'available_driver_ids' => $drivers->pluck('id')->all(),
        ]);

        if ($drivers->isEmpty()) {
            return [];
        }

        return $this->strategy->rank($drivers)->pluck('id')->all();
    }
}
