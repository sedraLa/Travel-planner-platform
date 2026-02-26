<?php

namespace Tests\Unit\Models;

use App\Models\DayActivity;
use App\Models\Hotel;
use App\Models\Trip;
use App\Models\TripDay;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripDayTest extends TestCase
{
    use RefreshDatabase;

    public function test_trip_day_relationships_with_counts(): void
    {
        $trip = Trip::factory()->create();
        $hotel = Hotel::factory()->create();

        $tripDay = TripDay::factory()->create([
            'trip_id' => $trip->id,
            'hotel_id' => $hotel->id,
        ]);

        DayActivity::factory()->count(2)->create(['trip_day_id' => $tripDay->id]);

        $this->assertInstanceOf(BelongsTo::class, $tripDay->trip());
        $this->assertInstanceOf(HasMany::class, $tripDay->activities());
        $this->assertInstanceOf(BelongsTo::class, $tripDay->hotel());
        $this->assertCount(2, $tripDay->activities);
        $this->assertTrue($tripDay->trip->is($trip));
        $this->assertTrue($tripDay->hotel->is($hotel));
    }
}
