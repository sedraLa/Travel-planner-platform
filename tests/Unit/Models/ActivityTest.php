<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\DayActivity;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_relationships_with_counts(): void
    {
        $destination = Destination::factory()->create();
        $activity = Activity::factory()->create(['destination_id' => $destination->id]);
        DayActivity::factory()->count(2)->create(['activity_id' => $activity->id]);

        $this->assertInstanceOf(BelongsTo::class, $activity->destination());
        $this->assertInstanceOf(HasMany::class, $activity->dayActivities());
        $this->assertTrue($activity->destination->is($destination));
        $this->assertCount(2, $activity->dayActivities);
    }
}
