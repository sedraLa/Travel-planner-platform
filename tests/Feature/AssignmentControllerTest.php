<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Http\Middleware\CheckDriverStatus;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\ShiftTemplate;
use App\Models\TransportVehicle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(CheckDriverStatus::class);
    }

    public function test_admin_can_open_assignment_create_page(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $response = $this->actingAs($admin)->get(route('assignments.create'));

        $response->assertOk()
            ->assertViewIs('assignments.create')
            ->assertViewHasAll(['vehicles', 'drivers', 'shiftTemplates', 'assignments']);
    }

    public function test_admin_can_create_assignment_when_no_time_conflict_exists(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $vehicle = TransportVehicle::factory()->create();
        $driver = Driver::factory()->create(['assignment_id' => null]);
        $shift = ShiftTemplate::factory()->create([
            'days_of_week' => ['mon'],
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        $response = $this->actingAs($admin)->post(route('assignments.store'), [
            'transport_vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'shift_template_id' => $shift->id,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('assignments', [
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shift->id,
        ]);

        $this->assertNotNull($driver->fresh()->assignment_id);
    }

    public function test_assignment_is_rejected_when_vehicle_has_overlapping_shift(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

        $vehicle = TransportVehicle::factory()->create();
        $driver = Driver::factory()->create(['assignment_id' => null]);

        $existingShift = ShiftTemplate::factory()->create([
            'days_of_week' => ['mon'],
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
        ]);

        Assignment::factory()->create([
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $existingShift->id,
        ]);

        $conflictingShift = ShiftTemplate::factory()->create([
            'days_of_week' => ['mon'],
            'start_time' => '11:00:00',
            'end_time' => '14:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('shift-templates.index'))
            ->post(route('assignments.store'), [
                'transport_vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'shift_template_id' => $conflictingShift->id,
            ]);

        $response->assertRedirect(route('shift-templates.index'));
        $response->assertSessionHasErrors('shift_template_id');

        $this->assertDatabaseCount('assignments', 1);
    }
}