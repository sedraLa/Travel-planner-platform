<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Http\Middleware\CheckDriverStatus;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\ShiftTemplate;
use App\Models\TransportReservation;
use App\Models\TransportVehicle;
use App\Models\User;
use App\Services\GeocodingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class VehicleAvailabilityTest extends TestCase
{
    use RefreshDatabase;   //teardown

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(CheckDriverStatus::class);

        $this->mock(GeocodingService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('geocodeAddress')
                ->andReturn([
                    'latitude' => 33.5138,
                    'longitude' => 36.2765,
                    'display_name' => 'Mock location',
                ]);
        });
    }

    public function test_happy_path_returns_vehicle_when_assignment_driver_shift_and_capacity_are_valid(): void
    {
        //arrange
        $pickupDatetime = Carbon::parse('next monday 10:00:00');
        $vehicle = TransportVehicle::factory()->create(['max_passengers' => 6]);

        $assignment = Assignment::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $this->createShiftFor($pickupDatetime)->id,
        ]);

        Driver::factory()->create(['assignment_id' => $assignment->id]);

        //act
        $response = $this->actingAs($this->createUser()) //actingAs to simulate user is logged in
            ->post(route('vehicle.search'), $this->payload($pickupDatetime, ['passengers' => 4]));

            //assert
        $response->assertOk() //HTTP 200
            ->assertViewIs('vehicles.index')
            ->assertViewHas('availableVehicles', fn ($vehicles) => $vehicles->contains('id', $vehicle->id)); //vehicle exist in results
    }

    public function test_vehicle_with_insufficient_max_passengers_is_excluded(): void
    {
        $pickupDatetime = Carbon::parse('next monday 10:00:00');
        $vehicle = TransportVehicle::factory()->create(['max_passengers' => 3]);

        $assignment = Assignment::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $this->createShiftFor($pickupDatetime)->id,
        ]);

        Driver::factory()->create(['assignment_id' => $assignment->id]);

        $response = $this->actingAs($this->createUser())
            ->from(route('vehicle.order'))
            ->post(route('vehicle.search'), $this->payload($pickupDatetime, ['passengers' => 5]));

        $response->assertRedirect(route('vehicle.order'))
            ->assertSessionHasErrors();
    }

    public function test_vehicle_without_assignment_is_excluded(): void
    {
        $pickupDatetime = Carbon::parse('next monday 10:00:00');
        TransportVehicle::factory()->create(['max_passengers' => 6]);

        $response = $this->actingAs($this->createUser())
            ->from(route('vehicle.order'))
            ->post(route('vehicle.search'), $this->payload($pickupDatetime));

        $response->assertRedirect(route('vehicle.order'))
            ->assertSessionHasErrors();
    }

    public function test_assignment_without_driver_is_excluded(): void
    {
        $pickupDatetime = Carbon::parse('next monday 10:00:00');
        $vehicle = TransportVehicle::factory()->create(['max_passengers' => 6]);

        Assignment::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $this->createShiftFor($pickupDatetime)->id,
        ]);

        $response = $this->actingAs($this->createUser())
            ->from(route('vehicle.order'))
            ->post(route('vehicle.search'), $this->payload($pickupDatetime, ['passengers' => 3]));

        $response->assertRedirect(route('vehicle.order'))
            ->assertSessionHasErrors();
    }

    public function test_category_and_type_filters_only_return_matching_vehicle(): void
    {
        $pickupDatetime = Carbon::parse('next monday 10:00:00');
        $shift = $this->createShiftFor($pickupDatetime);

        $matchingVehicle = TransportVehicle::factory()->create([
            'max_passengers' => 6,
            'category' => 'vip',
            'type' => 'suv',
        ]);

        $nonMatchingVehicle = TransportVehicle::factory()->create([
            'max_passengers' => 6,
            'category' => 'economy',
            'type' => 'sedan',
        ]);

        $matchingAssignment = Assignment::factory()->create([
            'transport_vehicle_id' => $matchingVehicle->id,
            'shift_template_id' => $shift->id,
        ]);
        Driver::factory()->create(['assignment_id' => $matchingAssignment->id]);

        $nonMatchingAssignment = Assignment::factory()->create([
            'transport_vehicle_id' => $nonMatchingVehicle->id,
            'shift_template_id' => $shift->id,
        ]);
        Driver::factory()->create(['assignment_id' => $nonMatchingAssignment->id]);

        $response = $this->actingAs($this->createUser())
            ->post(route('vehicle.search'), $this->payload($pickupDatetime, [
                'passengers' => 4,
                'category' => 'vip',
                'type' => 'suv',
            ]));

        $response->assertOk()
            ->assertViewHas('availableVehicles', function ($vehicles) use ($matchingVehicle, $nonMatchingVehicle) {
                return $vehicles->contains('id', $matchingVehicle->id)
                    && ! $vehicles->contains('id', $nonMatchingVehicle->id);
            });
    }

    public function test_drivers_can_be_ranked_by_lowest_total_trips_then_oldest_last_trip_time(): void
    {
        $drivers = Driver::factory()->count(3)->create();

        $drivers[0]->update([
            'total_trips_count' => 4,
            'last_trip_at' => Carbon::now()->subDays(2),
        ]);

        $drivers[1]->update([
            'total_trips_count' => 2,
            'last_trip_at' => Carbon::now()->subDays(1),
        ]);

        $drivers[2]->update([
            'total_trips_count' => 2,
            'last_trip_at' => Carbon::now()->subDays(10),
        ]);

        $ranked = Driver::query()
            ->orderBy('total_trips_count', 'asc')
            ->orderBy('last_trip_at', 'asc')
            ->get();

        $this->assertSame($drivers[2]->id, $ranked->first()->id);
    }

    public function test_vehicle_with_overlapping_reservation_is_excluded(): void
    {
        $pickupDatetime = Carbon::parse('next monday 10:00:00');
        $vehicle = TransportVehicle::factory()->create(['max_passengers' => 6]);

        $assignment = Assignment::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $this->createShiftFor($pickupDatetime)->id,
        ]);

        $driver = Driver::factory()->create(['assignment_id' => $assignment->id]);

        TransportReservation::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'pickup_datetime' => $pickupDatetime->copy()->subMinutes(20),
            'dropoff_datetime' => $pickupDatetime->copy()->addMinutes(40),
        ]);

        $response = $this->actingAs($this->createUser())
            ->from(route('vehicle.order'))
            ->post(route('vehicle.search'), $this->payload($pickupDatetime));

        $response->assertRedirect(route('vehicle.order'))
            ->assertSessionHasErrors();
    }

    public function test_assignment_is_excluded_when_pickup_time_is_outside_shift_time(): void
    {
        $pickupDatetime = Carbon::parse('next monday 10:00:00');
        $vehicle = TransportVehicle::factory()->create(['max_passengers' => 6]);

        $outsideShift = ShiftTemplate::factory()->create([
            'days_of_week' => [strtolower($pickupDatetime->format('D'))],
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
        ]);

        $assignment = Assignment::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $outsideShift->id,
        ]);

        Driver::factory()->create(['assignment_id' => $assignment->id]);

        $response = $this->actingAs($this->createUser())
            ->from(route('vehicle.order'))
            ->post(route('vehicle.search'), $this->payload($pickupDatetime));

        $response->assertRedirect(route('vehicle.order'))
            ->assertSessionHasErrors();
    }

    //helper methods
    //createshift for each test
    private function createShiftFor(Carbon $pickupDatetime): ShiftTemplate
    {
        return ShiftTemplate::factory()->create([
            'days_of_week' => [strtolower($pickupDatetime->format('D'))],
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
        ]);
    }

    //create user for each test
    private function createUser(): User
    {
        return User::factory()->create(['role' => UserRole::USER->value]);
    }

    //prepare data to send to form
    private function payload(Carbon $pickupDatetime, array $overrides = []): array
    {
        return array_merge([
            'pickup_location' => 'Damascus',
            'dropoff_location' => 'Aleppo',
            'pickup_datetime' => $pickupDatetime->toDateTimeString(),
            'passengers' => 2,
        ], $overrides);
    }
}