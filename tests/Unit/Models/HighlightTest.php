<?php

namespace Tests\Unit\Models;

use App\Models\Destination;
use App\Models\Highlight;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HighlightTest extends TestCase
{
    use RefreshDatabase;

    public function test_highlight_relationship_with_destination(): void
    {
        $destination = Destination::factory()->create();
        $highlight = Highlight::factory()->create(['destination_id' => $destination->id]);

        $this->assertInstanceOf(BelongsTo::class, $highlight->destination());
        $this->assertTrue($highlight->destination->is($destination));
    }
}
