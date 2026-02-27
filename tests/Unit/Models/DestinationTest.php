<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Destination;
use App\Models\DestinationImage;
use App\Models\Highlight;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationTest extends TestCase
{
    use RefreshDatabase;

    public function test_destination_relationships_with_counts(): void
    {
        $destination = Destination::factory()->create();

        DestinationImage::factory()->count(2)->create(['destination_id' => $destination->id]);
        Hotel::factory()->count(2)->create(['destination_id' => $destination->id]);
        Activity::factory()->count(2)->create(['destination_id' => $destination->id]);
        Highlight::factory()->count(2)->create(['destination_id' => $destination->id]);

        $this->assertInstanceOf(HasMany::class, $destination->images());
        $this->assertInstanceOf(HasMany::class, $destination->hotels());
        $this->assertInstanceOf(MorphMany::class, $destination->favorites());
        $this->assertInstanceOf(HasMany::class, $destination->activities());
        $this->assertInstanceOf(HasMany::class, $destination->highlights());

        $this->assertCount(2, $destination->images);
        $this->assertCount(2, $destination->hotels);
        $this->assertCount(2, $destination->activities);
        $this->assertCount(2, $destination->highlights);
    }
}
