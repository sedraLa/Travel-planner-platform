<?php

namespace Tests\Unit\Models;

use App\Models\Trip;
use App\Models\TripDay;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    public function test_trip_relationships_with_counts(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->create(['user_id' => $user->id]);
        TripDay::factory()->count(2)->create(['trip_id' => $trip->id]);

        $this->assertInstanceOf(BelongsTo::class, $trip->user());
        $this->assertInstanceOf(HasMany::class, $trip->days());
        $this->assertCount(2, $trip->days);
        $this->assertTrue($trip->user->is($user));
    }
}
