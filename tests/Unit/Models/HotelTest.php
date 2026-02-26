<?php

namespace Tests\Unit\Models;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_relationships_with_counts(): void
    {
        $destination = Destination::factory()->create();
        $hotel = Hotel::factory()->create(['destination_id' => $destination->id]);

        HotelImage::factory()->count(2)->create(['hotel_id' => $hotel->id]);
        Reservation::factory()->count(2)->create(['hotel_id' => $hotel->id]);

        $this->assertInstanceOf(BelongsTo::class, $hotel->destination());
        $this->assertInstanceOf(HasMany::class, $hotel->images());
        $this->assertInstanceOf(HasMany::class, $hotel->reservations());
        $this->assertInstanceOf(MorphMany::class, $hotel->favorites());

        $this->assertTrue($hotel->destination->is($destination));
        $this->assertCount(2, $hotel->images);
        $this->assertCount(2, $hotel->reservations);
    }
}
