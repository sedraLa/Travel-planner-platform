<?php

namespace Tests\Feature\TripStaffing;

use App\Jobs\TripStaffing\ProcessTripDriverMatchingJob;
use App\Jobs\TripStaffing\ProcessTripGuideMatchingJob;
use App\Models\DriverRequest;
use App\Models\GuideRequest;
use App\Models\Specialization;
use App\Models\Trip;
use App\Services\TripStaffing\DriverRankingService;
use App\Services\TripStaffing\GuideRankingService;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripStaffingFactoryScenariosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(CarbonImmutable::parse('2026-06-01 09:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_perfect_match_case_generates_and_sends_requests_only_to_matching_candidates(): void
    {
        $specialization = Specialization::factory()->create(['name' => 'City Tours']);

        $trip = Trip::factory()
            ->staffingWindow('2026-06-15', 3)
            ->create([
                'status' => 'staffing_in_progress',
                'guide_specialization_ids' => [$specialization->id],
                'requires_tour_leader' => true,
                'driver_vehicle_type' => 'van',
                'driver_vehicle_capacity' => 6,
            ]);

        $matchingGuide = \App\Models\Guide::factory()->matchingTrip($trip)->create();
        \App\Models\Guide::factory()->nonMatchingSpecialization($trip)->create();
        \App\Models\Guide::factory()->unavailableForTrip($trip)->create();
        \App\Models\Guide::factory()->matchingTrip($trip)->recentlyAssigned(2)->create();

        $matchingDriver = \App\Models\Driver::factory()->matchingTrip($trip)->create();
        \App\Models\Driver::factory()->failingCapacity($trip)->create();
        \App\Models\Driver::factory()->matchingTrip($trip)->failingLocation()->create();
        \App\Models\Driver::factory()->unavailableForTrip($trip)->create();

        ProcessTripGuideMatchingJob::dispatchSync($trip->id);
        ProcessTripDriverMatchingJob::dispatchSync($trip->id);

        $this->assertSame([$matchingGuide->id], $trip->fresh()->ranked_guide_ids);
        $this->assertSame([$matchingDriver->id], $trip->fresh()->ranked_driver_ids);

        $this->assertDatabaseHas('guide_requests', [
            'trip_id' => $trip->id,
            'guide_id' => $matchingGuide->id,
            'status' => 'pending',
            'chain_index' => 0,
        ]);

        $this->assertDatabaseHas('driver_requests', [
            'trip_id' => $trip->id,
            'driver_id' => $matchingDriver->id,
            'status' => 'pending',
            'chain_index' => 0,
        ]);

        $this->assertSame(1, GuideRequest::query()->where('trip_id', $trip->id)->count());
        $this->assertSame(1, DriverRequest::query()->where('trip_id', $trip->id)->count());
    }

    public function test_partial_match_case_where_guides_match_but_drivers_fail(): void
    {
        $specialization = Specialization::factory()->create(['name' => 'Historical Tours']);

        $trip = Trip::factory()
            ->staffingWindow('2026-07-01', 2)
            ->create([
                'status' => 'staffing_in_progress',
                'guide_specialization_ids' => [$specialization->id],
                'requires_tour_leader' => false,
                'driver_vehicle_type' => 'suv',
                'driver_vehicle_capacity' => 8,
            ]);

        \App\Models\Guide::factory()->matchingTrip($trip)->create();
        \App\Models\Driver::factory()->failingCapacity($trip)->create();
        \App\Models\Driver::factory()->matchingTrip($trip)->failingLocation()->create();

        ProcessTripGuideMatchingJob::dispatchSync($trip->id);
        ProcessTripDriverMatchingJob::dispatchSync($trip->id);

        $this->assertNotEmpty($trip->fresh()->ranked_guide_ids);
        $this->assertSame([], $trip->fresh()->ranked_driver_ids ?? []);
        $this->assertDatabaseCount('guide_requests', 1);
        $this->assertDatabaseCount('driver_requests', 0);
    }

    public function test_conflict_case_excludes_unavailable_guides_and_drivers(): void
    {
        $specialization = Specialization::factory()->create(['name' => 'Nature Exploration']);

        $trip = Trip::factory()->staffingWindow('2026-08-10', 4)->create([
            'status' => 'staffing_in_progress',
            'guide_specialization_ids' => [$specialization->id],
            'requires_tour_leader' => false,
            'driver_vehicle_type' => 'van',
            'driver_vehicle_capacity' => 5,
        ]);

        $availableGuide = \App\Models\Guide::factory()->matchingTrip($trip)->create();
        $conflictingGuide = \App\Models\Guide::factory()->unavailableForTrip($trip)->create();

        $availableDriver = \App\Models\Driver::factory()->matchingTrip($trip)->create();
        $conflictingDriver = \App\Models\Driver::factory()->unavailableForTrip($trip)->create();

        $guideIds = app(GuideRankingService::class)->rankedGuideIdsForTrip($trip);
        $driverIds = app(DriverRankingService::class)->rankedDriverIdsForTrip($trip);

        $this->assertContains($availableGuide->id, $guideIds);
        $this->assertNotContains($conflictingGuide->id, $guideIds);

        $this->assertContains($availableDriver->id, $driverIds);
        $this->assertNotContains($conflictingDriver->id, $driverIds);
    }

    public function test_ranking_case_orders_multiple_valid_candidates_by_score_rules(): void
    {
        $specialization = Specialization::factory()->create(['name' => 'Museum Tours']);

        $trip = Trip::factory()->staffingWindow('2026-09-20', 2)->create([
            'status' => 'staffing_in_progress',
            'guide_specialization_ids' => [$specialization->id],
            'requires_tour_leader' => false,
            'driver_vehicle_type' => 'van',
            'driver_vehicle_capacity' => 4,
        ]);

        $guideHigherPriority = \App\Models\Guide::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 1,
            'last_trip_at' => now()->subDays(40),
        ]);

        $guideLowerPriority = \App\Models\Guide::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 4,
            'last_trip_at' => now()->subDays(90),
        ]);

        $driverHigherPriority = \App\Models\Driver::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 1,
            'last_trip_at' => now()->subDays(15),
        ]);

        $driverLowerPriority = \App\Models\Driver::factory()->matchingTrip($trip)->create([
            'total_trips_count' => 3,
            'last_trip_at' => now()->subDays(120),
        ]);

        $guideIds = app(GuideRankingService::class)->rankedGuideIdsForTrip($trip);
        $driverIds = app(DriverRankingService::class)->rankedDriverIdsForTrip($trip);

        $this->assertSame([$guideHigherPriority->id, $guideLowerPriority->id], $guideIds);
        $this->assertSame([$driverHigherPriority->id, $driverLowerPriority->id], $driverIds);
    }
}
