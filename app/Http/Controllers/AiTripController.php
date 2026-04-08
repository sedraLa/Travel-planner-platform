<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\Activity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Trip;

class AiTripController extends Controller
{
    public function create()
    {
        $destinations = Destination::query()->orderBy('name')->get(['id', 'name', 'city', 'country']);
        $categories = Category::cases();

        return view('trips.ai.create', compact('destinations', 'categories'));
    }

    public function show(Trip $trip)
    {
        $trip->load(['primaryDestination', 'itineraryDestinations', 'days.activities.activity', 'days.hotel']);

        return view('trips.ai.show', compact('trip'));
    }

    public function editCompletion(Trip $trip)
    {
        $activeTab = request('tab', 'basics');

        $trip->load([
            'primaryDestination',
            'itineraryDestinations',
            'days.activities.activity',
            'days.hotel',
            'packages.includes',
            'packages.excludes',
            'packages.highlights',
            'packages.packageHotels.hotel',
            'schedules',
            'images',
        ]);

        $destinations = Destination::query()->orderBy('name')->get(['id', 'name']);
        $activities = Activity::query()->orderBy('name')->get(['id', 'name']);
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name']);

        return view('trips.ai.complete', compact('trip', 'activeTab', 'destinations', 'activities', 'hotels'));
    }
}
