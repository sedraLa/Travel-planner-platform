<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Http\Requests\AiTripUpdateRequest;
use App\Models\Activity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;

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
        if ($activeTab === 'guides') {
            $activeTab = 'overview';
        }

        return view('trips.ai.complete', compact('trip', 'activeTab', 'destinations', 'activities', 'hotels'));
    }

    public function edit(Trip $trip)
    {
        if (! $trip->is_ai_generated) {
            abort(404);
        }

        if ($trip->assigned_guide_id !== null) {
            return redirect()
                ->route('trips.index')
                ->with('error', 'This trip is already assigned to a guide and cannot be edited.');
        }

        return redirect()->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'basics']);
    }

    public function update(AiTripUpdateRequest $request, Trip $trip)
    {
        if (! $trip->is_ai_generated) {
            abort(404);
        }

        if ($trip->assigned_guide_id !== null) {
            return redirect()
                ->route('trips.index')
                ->with('error', 'This trip is already assigned to a guide and cannot be edited.');
        }

        $validated = $request->validated();
        $destinationIds = array_values(array_unique(array_map('intval', $validated['destination_ids'])));

        DB::transaction(function () use ($trip, $validated, $destinationIds): void {
            $trip->update([
                'destination_id' => $destinationIds[0],
                'description' => $validated['description'] ?? null,
                'duration_days' => (int) $validated['duration'],
                'category' => implode(',', $validated['categories']),
                'max_participants' => (int) $validated['max_participants'],
                'ai_prompt' => $validated['description'] ?? null,
            ]);
            $trip->itineraryDestinations()->sync(
                collect($destinationIds)->values()->mapWithKeys(fn (int $destinationId, int $index) => [
                    $destinationId => ['sort_order' => $index + 1],
                ])->all()
            );
        });

        return redirect()
            ->route('trips.index')
            ->with('success', 'Trip updated successfully.');
    }
}
