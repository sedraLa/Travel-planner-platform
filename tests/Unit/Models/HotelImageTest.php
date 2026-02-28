<?php

namespace Tests\Unit\Models;

use App\Models\Hotel;
use App\Models\HotelImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_image_relationship_with_parent(): void
    {
        $hotel = Hotel::factory()->create();
        $hotelImage = HotelImage::factory()->create(['hotel_id' => $hotel->id]);

        $this->assertInstanceOf(BelongsTo::class, $hotelImage->hotel());
        $this->assertTrue($hotelImage->hotel->is($hotel));
    }
}
