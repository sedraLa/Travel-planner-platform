<?php

namespace Tests\Unit\Models;

use App\Models\Destination;
use App\Models\Favorite;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\TransportReservation;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_trips_relationship_with_count(): void
    {
        $user = User::factory()->create();
        Trip::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertInstanceOf(HasMany::class, $user->trips());
        $this->assertCount(3, $user->trips);
    }

    public function test_user_other_relationships_with_counts(): void
    {
        $user = User::factory()->create();

        Reservation::factory()->count(2)->create(['user_id' => $user->id]);
        Payment::factory()->count(2)->create(['user_id' => $user->id]);
        TransportReservation::factory()->count(2)->create(['user_id' => $user->id]);

        $destinationOne = Destination::factory()->create();
        $destinationTwo = Destination::factory()->create();

        Favorite::factory()->create([
            'user_id' => $user->id,
            'favoritable_type' => Destination::class,
            'favoritable_id' => $destinationOne->id,
        ]);

        Favorite::factory()->create([
            'user_id' => $user->id,
            'favoritable_type' => Destination::class,
            'favoritable_id' => $destinationTwo->id,
        ]);

        $this->assertInstanceOf(HasMany::class, $user->reservations());
        $this->assertInstanceOf(HasMany::class, $user->payments());
        $this->assertInstanceOf(HasMany::class, $user->transport_reservations());
        $this->assertInstanceOf(HasMany::class, $user->favorites());
        $this->assertInstanceOf(MorphToMany::class, $user->favoriteDestinations());
        $this->assertInstanceOf(MorphToMany::class, $user->favoriteHotels());
        $this->assertInstanceOf(HasOne::class, $user->driver());

        $this->assertCount(2, $user->reservations);
        $this->assertCount(2, $user->payments);
        $this->assertCount(2, $user->transport_reservations);
        $this->assertCount(2, $user->favorites);
    }

    public function test_user_full_name_accessor(): void
    {
        $user = User::factory()->make([
            'name' => 'Ahmad',
            'last_name' => 'Khaled',
        ]);

        $this->assertSame('Ahmad Khaled', $user->full_name);
    }
}
