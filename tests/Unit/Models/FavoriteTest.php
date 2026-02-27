<?php

namespace Tests\Unit\Models;

use App\Models\Destination;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_relationships_with_morph_target(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'favoritable_type' => Destination::class,
            'favoritable_id' => $destination->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $favorite->user());
        $this->assertInstanceOf(MorphTo::class, $favorite->favoritable());
        $this->assertTrue($favorite->user->is($user));
        $this->assertTrue($favorite->favoritable->is($destination));
    }
}
