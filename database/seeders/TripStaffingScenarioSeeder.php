<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Jobs\TripStaffing\StartTripStaffingJob;
use App\Models\Driver;
use App\Models\Guide;
use App\Models\Specialization;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class TripStaffingScenarioSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin.staffing@example.com'],
            [
                'name' => 'Staffing',
                'last_name' => 'Admin',
                'phone_number' => '70000000',
                'country' => 'Lebanon',
                'password' => bcrypt('password'),
                'role' => UserRole::ADMIN->value,
                'email_verified_at' => now(),
            ]
        );

        $destination = \App\Models\Destination::query()->firstOrCreate(
            ['name' => 'Beirut Downtown'],
            [
                'city' => 'Beirut',
                'country' => 'Lebanon',
                'description' => 'Historic downtown with museums, local culture, and sea views.',
                'location_details' => 'Downtown district',
                'iata_code' => 'BEY',
                'timezone' => 'Asia/Beirut',
                'language' => 'ar',
                'currency' => 'LBP',
                'nearest_airport' => 'Rafic Hariri International Airport',
                'best_time_to_visit' => 'Spring',
                'emergency_numbers' => '112',
                'local_tip' => 'Try local breakfast early in the morning.',
            ]
        );

        $cityTours = Specialization::query()->firstOrCreate(['name' => 'City Tours']);
        $historicalTours = Specialization::query()->firstOrCreate(['name' => 'Historical Tours']);
        $natureTours = Specialization::query()->firstOrCreate(['name' => 'Nature Exploration']);

        $this->seedPerfectMatchScenario($destination->id, $cityTours->id);
        $this->seedPartialMatchScenario($destination->id, $historicalTours->id);
        $this->seedConflictScenario($destination->id, $natureTours->id);
        $this->seedRankingScenario($destination->id, $cityTours->id);
    }

    private function seedPerfectMatchScenario(int $destinationId, int $specializationId): void
    {
        $trip = Trip::factory()->staffingWindow('2026-06-15', 3)->create([
            'destination_id' => $destinationId,
            'name' => 'Staffing Demo - Perfect Match',
            'slug' => 'staffing-demo-perfect-match',
            'status' => 'ready_for_assignment',
            'guide_specialization_ids' => [$specializationId],
            'requires_tour_leader' => true,
            'driver_vehicle_type' => 'van',
            'driver_vehicle_capacity' => 6,
            'driver_trip_type' => 'intercity',
            'driver_road_type' => 'highway',
        ]);

        Guide::factory()->matchingTrip($trip)->create(['total_trips_count' => 1]);
        Guide::factory()->nonMatchingSpecialization($trip)->create();
        Guide::factory()->unavailableForTrip($trip)->create();
        Guide::factory()->matchingTrip($trip)->recentlyAssigned(2)->create();

        Driver::factory()->matchingTrip($trip)->create(['total_trips_count' => 1]);
        Driver::factory()->failingCapacity($trip)->create();
        Driver::factory()->matchingTrip($trip)->failingLocation()->create();
        Driver::factory()->unavailableForTrip($trip)->create();

        StartTripStaffingJob::dispatch($trip->id);
    }

    private function seedPartialMatchScenario(int $destinationId, int $specializationId): void
    {
        $trip = Trip::factory()->staffingWindow('2026-07-03', 2)->create([
            'destination_id' => $destinationId,
            'name' => 'Staffing Demo - Partial Match',
            'slug' => 'staffing-demo-partial-match',
            'status' => 'ready_for_assignment',
            'guide_specialization_ids' => [$specializationId],
            'requires_tour_leader' => false,
            'driver_vehicle_type' => 'suv',
            'driver_vehicle_capacity' => 8,
            'driver_trip_type' => 'intercity',
            'driver_road_type' => 'city',
        ]);

        Guide::factory()->matchingTrip($trip)->create();

        Driver::factory()->failingCapacity($trip)->create();
        Driver::factory()->matchingTrip($trip)->failingLocation()->create();

        StartTripStaffingJob::dispatch($trip->id);
    }

    private function seedConflictScenario(int $destinationId, int $specializationId): void
    {
        $trip = Trip::factory()->staffingWindow('2026-08-10', 4)->create([
            'destination_id' => $destinationId,
            'name' => 'Staffing Demo - Conflict',
            'slug' => 'staffing-demo-conflict',
            'status' => 'ready_for_assignment',
            'guide_specialization_ids' => [$specializationId],
            'requires_tour_leader' => false,
            'driver_vehicle_type' => 'van',
            'driver_vehicle_capacity' => 5,
            'driver_trip_type' => 'mountain',
            'driver_road_type' => 'mixed',
        ]);

        Guide::factory()->matchingTrip($trip)->create();
        Guide::factory()->unavailableForTrip($trip)->create();

        Driver::factory()->matchingTrip($trip)->create();
        Driver::factory()->unavailableForTrip($trip)->create();

        StartTripStaffingJob::dispatch($trip->id);
    }

    private function seedRankingScenario(int $destinationId, int $specializationId): void
    {
        $trip = Trip::factory()->staffingWindow('2026-09-20', 2)->create([
            'destination_id' => $destinationId,
            'name' => 'Staffing Demo - Ranking',
            'slug' => 'staffing-demo-ranking',
            'status' => 'ready_for_assignment',
            'guide_specialization_ids' => [$specializationId],
            'requires_tour_leader' => false,
            'driver_vehicle_type' => 'van',
            'driver_vehicle_capacity' => 4,
            'driver_trip_type' => 'intercity',
            'driver_road_type' => 'highway',
        ]);

        Guide::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 1,
            'last_trip_at' => now()->subDays(60),
        ]);

        Guide::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 4,
            'last_trip_at' => now()->subDays(120),
        ]);

        Driver::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 1,
            'last_trip_at' => now()->subDays(30),
        ]);

        Driver::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 3,
            'last_trip_at' => now()->subDays(90),
        ]);

        StartTripStaffingJob::dispatch($trip->id);
    }
}
