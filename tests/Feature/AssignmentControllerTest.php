<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use App\Models\Driver;
use App\Models\Assignment;
use App\Models\ShiftTemplate;
use App\Models\TransportVehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Notifications\AssignmentNotificationService;
use App\Models\User;


class AssignmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء مستخدم admin
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // تسجيل الدخول
        $this->actingAs($user);

        // Mock للخدمة حتى لا ترسل notifications
        $mock = Mockery::mock(AssignmentNotificationService::class);
        $mock->shouldIgnoreMissing();

        $this->app->instance(AssignmentNotificationService::class, $mock);
    }

    /** @test */
public function it_filters_assignments_by_vehicle_model()
{
    $vehicle1 = TransportVehicle::factory()->create(['car_model' => 'Toyota']);
    $vehicle2 = TransportVehicle::factory()->create(['car_model' => 'BMW']);

    $shift = ShiftTemplate::factory()->create();
    $driver = Driver::factory()
    ->for(User::factory())
    ->create(['status' => 'approved']);

    Assignment::factory()->create([
        'transport_vehicle_id' => $vehicle1->id,
        'shift_template_id' => $shift->id,
        'driver_id' => $driver->id
    ]);

    Assignment::factory()->create([
        'transport_vehicle_id' => $vehicle2->id,
        'shift_template_id' => $shift->id,
       'driver_id' => Driver::factory()->for(User::factory())->create(['status' => 'approved'])->id
    ]);

    $response = $this->get('/admin/assignments?search=Toyota');

    $response->assertStatus(200);
    $response->assertViewHas('assignments', function ($assignments) {
        return $assignments->count() === 1;
    });
}

/** @test */
public function update_prevents_driver_having_multiple_assignments()
{
    $vehicle1 = TransportVehicle::factory()->create();
    $vehicle2 = TransportVehicle::factory()->create();

    $shift = ShiftTemplate::factory()->create();

    $driver = Driver::factory()->create(['status' => 'approved']);

    Assignment::factory()->create([
        'driver_id' => $driver->id,
        'transport_vehicle_id' => $vehicle1->id,
        'shift_template_id' => $shift->id
    ]);

    $assignment = Assignment::factory()->create([
        'driver_id' => Driver::factory()->create(['status' => 'approved'])->id,
        'transport_vehicle_id' => $vehicle2->id,
        'shift_template_id' => $shift->id
    ]);

    $response = $this->put("/admin/assignments/update/{$assignment->id}", [
        'driver_id' => $driver->id,
        'transport_vehicle_id' => $vehicle2->id,
        'shift_template_id' => $shift->id
    ]);

    $response->assertSessionHasErrors(['driver_id']);
}

/** @test */
public function it_filters_assignments_by_driver_name()
{
    $vehicle1 = TransportVehicle::factory()->create();
    $vehicle2 = TransportVehicle::factory()->create();

    $shift = ShiftTemplate::factory()->create();

    $user1 = User::factory()->create([
        'name' => 'John',
        'last_name' => 'Smith'
    ]);

    $user2 = User::factory()->create([
        'name' => 'Mike',
        'last_name' => 'Brown'
    ]);

    $driver1 = Driver::factory()->create([
        'user_id' => $user1->id,
        'status' => 'approved'
    ]);

    $driver2 = Driver::factory()->create([
        'user_id' => $user2->id,
        'status' => 'approved'
    ]);

    Assignment::factory()->create([
        'driver_id' => $driver1->id,
        'transport_vehicle_id' => $vehicle1->id,
        'shift_template_id' => $shift->id
    ]);

    Assignment::factory()->create([
        'driver_id' => $driver2->id,
        'transport_vehicle_id' => $vehicle2->id,
        'shift_template_id' => $shift->id
    ]);

    $response = $this->get('/admin/assignments?search=John');

    $response->assertStatus(200);
}



    /** @test */
    public function it_creates_assignment_successfully()
    {
        $vehicle = TransportVehicle::factory()->create();
        $shift = ShiftTemplate::factory()->create([
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'days_of_week' => ['mon']
        ]);
        $driver = Driver::factory()->create(['status' => 'approved']);

        $response = $this->post('/admin/assignments/store', [ // <--- تعديل هنا
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shift->id,
            'driver_id' => $driver->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Assignment created successfully.');

        $this->assertDatabaseHas('assignments', [
            'driver_id' => $driver->id,
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shift->id
        ]);
    }

    /** @test */
    public function driver_cannot_have_more_than_one_assignment()
    {
        $vehicle1 = TransportVehicle::factory()->create();
        $vehicle2 = TransportVehicle::factory()->create();
        $shift = ShiftTemplate::factory()->create([
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'days_of_week' => ['mon']
        ]);
        $driver = Driver::factory()->create(['status' => 'approved']);

        //create first assignment for the driver
        Assignment::factory()->create([
            'driver_id' => $driver->id,
            'transport_vehicle_id' => $vehicle1->id,
            'shift_template_id' => $shift->id
        ]);

        //create another assignment for the same driver
        $response = $this->post('/admin/assignments/store', [
            'transport_vehicle_id' => $vehicle2->id,
            'shift_template_id' => $shift->id,
            'driver_id' => $driver->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['driver_id']);
    }

    /** @test */
    public function vehicle_cannot_have_overlapping_shift()
    {
        $vehicle = TransportVehicle::factory()->create();
        $shift1 = ShiftTemplate::factory()->create(['start_time' => '08:00:00', 'end_time' => '12:00:00', 'days_of_week' => ['mon']]);
        $shift2 = ShiftTemplate::factory()->create(['start_time' => '10:00:00', 'end_time' => '14:00:00', 'days_of_week' => ['mon']]);
        $driver1 = Driver::factory()->create(['status' => 'approved']);
        $driver2 = Driver::factory()->create(['status' => 'approved']);

        Assignment::factory()->create([
            'driver_id' => $driver1->id,
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shift1->id
        ]);

        $response = $this->post('/admin/assignments/store', [
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shift2->id,
            'driver_id' => $driver2->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['shift_template_id']);
    }

    /** @test */
    public function vehicle_can_have_non_overlapping_shift()
    {
        $vehicle = TransportVehicle::factory()->create();
        $shift1 = ShiftTemplate::factory()->create(['start_time' => '08:00:00', 'end_time' => '12:00:00', 'days_of_week' => ['mon']]);
        $shift2 = ShiftTemplate::factory()->create(['start_time' => '13:00:00', 'end_time' => '17:00:00', 'days_of_week' => ['mon']]);
        $driver1 = Driver::factory()->create(['status' => 'approved']);
        $driver2 = Driver::factory()->create(['status' => 'approved']);

        Assignment::factory()->create([
            'driver_id' => $driver1->id,
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shift1->id
        ]);

        $response = $this->post('/admin/assignments/store', [ // <--- تعديل هنا
            'transport_vehicle_id' => $vehicle->id,
            'shift_template_id' => $shift2->id,
            'driver_id' => $driver2->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Assignment created successfully.');
        $this->assertDatabaseCount('assignments', 2);
    }

    /** @test */
public function vehicle_can_have_same_time_shift_if_days_are_different()
{
    $vehicle = TransportVehicle::factory()->create();

    $shift1 = ShiftTemplate::factory()->create([
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'days_of_week' => ['mon']
    ]);

    $shift2 = ShiftTemplate::factory()->create([
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'days_of_week' => ['tue']
    ]);

    $driver1 = Driver::factory()->create(['status' => 'approved']);
    $driver2 = Driver::factory()->create(['status' => 'approved']);

    Assignment::factory()->create([
        'transport_vehicle_id' => $vehicle->id,
        'shift_template_id' => $shift1->id,
        'driver_id' => $driver1->id
    ]);

    $response = $this->post('/admin/assignments/store', [
        'transport_vehicle_id' => $vehicle->id,
        'shift_template_id' => $shift2->id,
        'driver_id' => $driver2->id
    ]);

    $response->assertSessionHas('success');
}

    /** @test */
    public function it_updates_assignment_successfully()
    {
        $vehicle1 = TransportVehicle::factory()->create();
        $vehicle2 = TransportVehicle::factory()->create();
        $shift1 = ShiftTemplate::factory()->create(['start_time' => '08:00:00', 'end_time' => '12:00:00', 'days_of_week' => ['mon']]);
        $shift2 = ShiftTemplate::factory()->create(['start_time' => '13:00:00', 'end_time' => '17:00:00', 'days_of_week' => ['mon']]);
        $driver = Driver::factory()->create(['status' => 'approved']);

        $assignment = Assignment::factory()->create([
            'driver_id' => $driver->id,
            'transport_vehicle_id' => $vehicle1->id,
            'shift_template_id' => $shift1->id
        ]);

        $response = $this->put("/admin/assignments/update/{$assignment->id}", [
            'transport_vehicle_id' => $vehicle2->id,
            'shift_template_id' => $shift2->id,
            'driver_id' => $driver->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Assignment updated successfully.');
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'transport_vehicle_id' => $vehicle2->id
        ]);
    }

    /** @test */
    public function it_deletes_assignment()
    {
        $assignment = Assignment::factory()->create();
        $response = $this->delete("/admin/assignments/{$assignment->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Assignment deleted successfully.');
        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
    }
}
