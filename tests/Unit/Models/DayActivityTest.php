<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\DayActivity;
use App\Models\TripDay;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DayActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_day_activity_relationships_with_bound_models(): void
    {
        $tripDay = TripDay::factory()->create();
        $activity = Activity::factory()->create();

        $dayActivity = DayActivity::factory()->create([
            'trip_day_id' => $tripDay->id,
            'activity_id' => $activity->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $dayActivity->tripDay());
        $this->assertInstanceOf(BelongsTo::class, $dayActivity->activity());
        $this->assertTrue($dayActivity->tripDay->is($tripDay));
        $this->assertTrue($dayActivity->activity->is($activity));
    }
}
