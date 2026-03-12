<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ShiftTemplate;
use App\Models\Assignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;




class ShiftTemplateTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_shifTemplate_relationships_with_count(): void
    {
        //prepare data
        // create shift template
        $shiftTemplate = ShiftTemplate::factory()->create();
        //create 2 assignments linked to it 
        $assignment = Assignment::factory()->count(2)->create([
            'shift_template_id' => $shiftTemplate->id]);

    // relationship type
    $this->assertInstanceOf(HasMany::class, $shiftTemplate->assignments());

    // relationship count
    $this->assertCount(2, $shiftTemplate->assignments);
        
    }
}
