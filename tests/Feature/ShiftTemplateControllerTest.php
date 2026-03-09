<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Assignment;
use App\Models\ShiftTemplate;
use App\Models\User;

class ShiftTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // تسجيل الدخول
        $this->actingAs($user);
    }

    /** @test */
    public function it_gets_all_shifts_templates(): void
    {
        $shift1 = ShiftTemplate::factory()->create(['name' => 'Shift 1']);
        $shift2 = ShiftTemplate::factory()->create(['name' => 'Shift 2']);

        $response = $this->get(route('shift-templates.index'));

        $response->assertStatus(200);
        $response->assertViewIs('shifts.shifttemplate');
        $response->assertViewHas('shiftTemplates');

        $this->assertDatabaseHas('shift_templates', ['name' => 'Shift 1']);
        $this->assertDatabaseHas('shift_templates', ['name' => 'Shift 2']);
    }

    /** @test */
    public function it_gets_shift_templates_depending_on_search_criteria(): void
    {
        $shift3 = ShiftTemplate::factory()->create(['name' => 'shift3']);
        $shift4 = ShiftTemplate::factory()->create(['name' => 'shift4']);

        $response = $this->get(route('shift-templates.index', ['search' => 'shift3']));

        $response->assertStatus(200);
        $response->assertViewIs('shifts.shifttemplate');

        $response->assertViewHas('shiftTemplates', function ($shiftTemplates) use ($shift3, $shift4) {
            $containsShift3 = $shiftTemplates->contains($shift3);
            $containsShift4 = $shiftTemplates->contains($shift4);

            return $containsShift3 && ! $containsShift4;
        });
    }

    /** @test */
    public function it_stores_shift_template_successfully(): void
    {
        $data = [
            'name' => 'Morning Shift',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'days_of_week' => ['Monday','Tuesday'],
        ];

        $response = $this->post(route('shift-templates.store'), $data);

        $response->assertRedirect(route('shift-templates.index'));

        $response->assertSessionHas('success', 'Shift template created successfully.');

        $this->assertDatabaseHas('shift_templates', [
            'name' => 'Morning Shift',
            'start_time' => '08:00',
            'end_time' => '12:00',
        ]);
    }

    /** @test */
    public function it_prevents_creating_duplicate_shift_template(): void
    {
        ShiftTemplate::factory()->create([
            'name' => 'Morning Shift',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'days_of_week' => ['Monday','Tuesday'],
        ]);

        $data = [
            'name' => 'Another Morning Shift',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'days_of_week' => ['Monday','Tuesday'],
        ];

        $response = $this->from('/admin/shift-templates/create')
            ->post(route('shift-templates.store'), $data);

        $response->assertSessionHasErrors(['start_time']);

        $this->assertDatabaseCount('shift_templates', 1);
    }

    /** @test */
    public function admin_can_delete_shift_template(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $shift = ShiftTemplate::factory()->create();

        $response = $this->delete(route('shift-templates.destroy', $shift->id));

        $response->assertRedirect(route('shift-templates.index'));

        $response->assertSessionHas('success', 'Shift template deleted successfully.');

        $this->assertDatabaseMissing('shift_templates', [
            'id' => $shift->id
        ]);
    }

    /** @test */
    public function it_prevents_deleting_shift_template_with_assignments(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->actingAs($admin);

        $shift = ShiftTemplate::factory()->create();

        Assignment::factory()->create([
            'shift_template_id' => $shift->id
        ]);

        $response = $this->delete(route('shift-templates.destroy', $shift->id));

        $response->assertRedirect(route('shift-templates.index'));

        $response->assertSessionHas('error', 'Cannot delete this shift template because it has assignments.');

        $this->assertDatabaseHas('shift_templates', [
            'id' => $shift->id
        ]);
    }
}