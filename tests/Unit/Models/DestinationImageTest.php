<?php

namespace Tests\Unit\Models;

use App\Models\Destination;
use App\Models\DestinationImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_destination_image_relationship_with_parent(): void
    {
        $destination = Destination::factory()->create();
        $image = DestinationImage::factory()->create(['destination_id' => $destination->id]);

        $this->assertInstanceOf(BelongsTo::class, $image->destination());
        $this->assertTrue($image->destination->is($destination));
    }
}
