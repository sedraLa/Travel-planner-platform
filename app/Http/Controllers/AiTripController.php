<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\DayActivity;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\TripDay;
use App\Services\GroqTripPlannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AiTripController extends Controller
{
    public function __construct(protected GroqTripPlannerService $groqService)
    {
    }

    public function create()
    {
        $destinations = Destination::query()->orderBy('name')->get(['id', 'name', 'city', 'country']);
        $categories = Category::cases();

        return view('trips.ai.create', compact('destinations', 'categories'));
    }

    public function generate(Request $request)
    {
        $allowedCategories = implode(',', Category::values());

        $validated = $request->validate([
            'destination_ids' => 'required|array|min:1',
            'destination_ids.*' => 'required|integer|exists:destinations,id',
            'description' => 'required|string|max:1000',
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|string|in:' . $allowedCategories,
            'max_participants' => 'required|integer|min:1',
            'budget' => 'nullable|numeric|min:0',
            'duration' => 'required|integer|min:1|max:30',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'language' => 'nullable|in:en,ar',
        ]);

        $validated['destination_ids'] = array_values(array_unique($validated['destination_ids']));
        $validated['categories'] = array_values(array_unique($validated['categories']));
        $language = $validated['language'] ?? 'en';

        $plan = $this->groqService->generateTripPlan($validated, $language);

        if (! $plan) {
            return back()->withErrors(['api_error' => 'Failed to generate trip from Groq API.']);
        }

        $trip = DB::transaction(function () use ($validated, $plan) {
            $name = Str::limit($plan['trip_name'] ?: $validated['description'], 120, '');
            $slugBase = Str::slug($name ?: 'ai-trip');
            $primaryDestinationId = (int) $validated['destination_ids'][0];

            $trip = Trip::create([
                'destination_id' => $primaryDestinationId,
                'name' => $name,
                'slug' => $this->nextUniqueSlug($slugBase),
                'duration_days' => (int) $validated['duration'],
                'category' => implode(',', $validated['categories']),
                'max_participants' => (int) $validated['max_participants'],
                // Meeting point is admin-managed (OpenStreetMap + geocoding flow), not AI-managed.
                'meeting_point_description' => null,
                'meeting_point_address' => null,
                'is_ai_generated' => true,
                'ai_prompt' => $validated['description'],
                'status' => 'draft',
            ]);

            $trip->destinations()->sync(
                collect($validated['destination_ids'])->values()->mapWithKeys(fn (int $destinationId, int $index) => [
                    $destinationId => ['sort_order' => $index + 1],
                ])->all()
            );

            foreach ($plan['days'] as $day) {
                $tripDay = TripDay::create([
                    'trip_id' => $trip->id,
                    'day_number' => (int) $day['day_number'],
                    'title' => $day['title'],
                    'description' => $day['description'],
                    'highlights' => [], //admin-managed
                    'hotel_id' => $day['hotel_id'] ?? null,
                ]);

                foreach ($day['activities'] as $activity) {
                    DayActivity::create([
                        'trip_day_id' => $tripDay->id,
                        'activity_id' => (int) $activity['activity_id'],
                        'start_time' => $activity['start_time'] ?? null,
                        'end_time' => $activity['end_time'] ?? null,
                        'notes' => $activity['notes'] ?? null,
                    ]);
                }
            }

            return $trip;
        });

        return redirect()->route('ai.show', $trip->id)->with('success', 'Your AI trip has been generated and saved!');
    }

    public function show(Trip $trip)
    {
        $trip->load(['destination', 'destinations', 'days.activities.activity', 'days.hotel']);

        return view('trips.ai.show', compact('trip'));
    }

    protected function nextUniqueSlug(string $slugBase): string
    {
        $slug = $slugBase ?: 'ai-trip'; //for empty name use ai-trip
        $counter = 1;

        while (Trip::query()->where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
