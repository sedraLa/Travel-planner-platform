<?php

namespace Tests\Unit\Services\TransportReservation;

use App\Domain\TransportReservation\Ranking\LastTripAndTripsCountStrategy;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\ShiftTemplate;
use App\Models\TransportReservation;
use App\Models\TransportVehicle;
use App\Models\User;
use App\Services\TransportReservation\DriverRankingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverRankingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_drivers_by_vehicle_category_type_and_capacity(): void
    {
        $pickup = Carbon::parse('next monday 10:00:00');
        $shift = ShiftTemplate::factory()->create([
            'days_of_week' => [strtolower($pickup->format('D'))],
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
        ]);

        $matchingVehicle = TransportVehicle::factory()->create([
            'category' => 'vip',
            'type' => 'suv',
            'max_passengers' => 6,
        ]);
        $otherVehicle = TransportVehicle::factory()->create([
            'category' => 'economy',
            'type' => 'sedan',
            'max_passengers' => 6,
        ]);

        $matchingDriver = $this->createApprovedDriverForVehicle($matchingVehicle->id, $shift->id);
        $this->createApprovedDriverForVehicle($otherVehicle->id, $shift->id);

        $reservation = TransportReservation::factory()->create([
            'pickup_datetime' => $pickup,
            'dropoff_datetime' => $pickup->copy()->addHour(),
            'passengers' => 4,
            'preferred_category' => 'vip',
            'preferred_type' => 'suv',
            'transport_vehicle_id' => null,
            'driver_id' => null,
        ]);

        $service = new DriverRankingService(new LastTripAndTripsCountStrategy());
        $rankedIds = $service->rankedDriverIdsForReservation($reservation);

        $this->assertSame([$matchingDriver->id], $rankedIds);
    }

    public function test_it_excludes_overlapping_vehicles_and_ranks_by_fewer_trips_then_oldest_last_trip(): void
    {
        $pickup = Carbon::parse('next monday 10:00:00');
        $shift = ShiftTemplate::factory()->create([
            'days_of_week' => [strtolower($pickup->format('D'))],
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
        ]);

        $busyVehicle = TransportVehicle::factory()->create(['max_passengers' => 6]);
        $freeVehicle1 = TransportVehicle::factory()->create(['max_passengers' => 6]);
        $freeVehicle2 = TransportVehicle::factory()->create(['max_passengers' => 6]);

        $busyDriver = $this->createApprovedDriverForVehicle($busyVehicle->id, $shift->id, [
            'total_trips_count' => 1,
            'last_trip_at' => now()->subDays(20),
        ]);

        $driverWithMoreTrips = $this->createApprovedDriverForVehicle($freeVehicle1->id, $shift->id, [
            'total_trips_count' => 5,
            'last_trip_at' => now()->subDays(30),
        ]);

        $driverWithFewerTrips = $this->createApprovedDriverForVehicle($freeVehicle2->id, $shift->id, [
            'total_trips_count' => 2,
            'last_trip_at' => now()->subDays(2),
        ]);

        TransportReservation::factory()->create([
            'transport_vehicle_id' => $busyVehicle->id,
            'driver_id' => $busyDriver->id,
            'pickup_datetime' => $pickup->copy()->subMinutes(10),
            'dropoff_datetime' => $pickup->copy()->addMinutes(45),
            'status' => 'driver_assigned',
        ]);

        $reservation = TransportReservation::factory()->create([
            'pickup_datetime' => $pickup,
            'dropoff_datetime' => $pickup->copy()->addHour(),
            'passengers' => 2,
            'transport_vehicle_id' => null,
            'driver_id' => null,
        ]);

        $service = new DriverRankingService(new LastTripAndTripsCountStrategy());
        $rankedIds = $service->rankedDriverIdsForReservation($reservation);

        $this->assertSame([$driverWithFewerTrips->id, $driverWithMoreTrips->id], $rankedIds);
    }

    private function createApprovedDriverForVehicle(int $vehicleId, int $shiftId, array $driverOverrides = []): Driver
    {
        $assignment = Assignment::factory()->create([
            'transport_vehicle_id' => $vehicleId,
            'shift_template_id' => $shiftId,
        ]);

        return Driver::factory()->create(array_merge([
            'status' => 'approved',
            'assignment_id' => $assignment->id,
            'user_id' => User::factory()->create(['role' => 'driver'])->id,
        ], $driverOverrides));
    }
}
