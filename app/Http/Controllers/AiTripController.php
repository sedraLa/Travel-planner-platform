<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\Activity;
use App\Models\DayActivity;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\TripExclude;
use App\Models\TripHighlight;
use App\Models\TripImage;
use App\Models\TripInclude;
use App\Models\TripPackage;
use App\Models\TripPackageHotel;
use App\Models\TripSchedule;
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
                'description' => $plan['trip_description'] ?? null,
                'duration_days' => (int) $validated['duration'],
                'category' => implode(',', $validated['categories']),
                'max_participants' => (int) $validated['max_participants'],
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
                    'highlights' => [],
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

            $this->bootstrapPackageFromAiPlan($trip);

            return $trip;
        });

        return redirect()->route('trip.complete.edit', $trip)
            ->with('success', 'Your AI trip has been generated as draft. Complete the remaining details and save each tab.');
    }

    public function show(Trip $trip)
    {
        $trip->load(['destination', 'destinations', 'days.activities.activity', 'days.hotel']);

        return view('trips.ai.show', compact('trip'));
    }

    public function editCompletion(Trip $trip)
    {
        $activeTab = request('tab', 'basics');

        $trip->load([
            'destination',
            'destinations',
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

    public function saveBasics(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:30',
            'category' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'meeting_point_description' => 'nullable|string',
            'meeting_point_address' => 'nullable|string|max:255',
            'status' => 'required|string|in:draft,published',
        ]);

        $trip->update($validated);

        return redirect()->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'basics'])
            ->with('success', 'Basics saved successfully.');
    }

    public function saveDaysActivities(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'days' => 'required|array|min:1',
            'days.*.day_number' => 'required|integer|min:1',
            'days.*.title' => 'nullable|string|max:255',
            'days.*.description' => 'nullable|string',
            'days.*.hotel_id' => 'nullable|exists:hotels,id',
            'days.*.activities' => 'nullable|array',
            'days.*.activities.*.id' => 'nullable|integer|exists:day_activities,id',
            'days.*.activities.*.activity_id' => 'nullable|exists:activities,id',
            'days.*.activities.*.start_time' => 'nullable|date_format:H:i',
            'days.*.activities.*.end_time' => 'nullable|date_format:H:i',
            'days.*.activities.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($trip, $validated) {
            foreach ($validated['days'] as $dayPayload) {
                $tripDay = TripDay::query()->updateOrCreate(
                    ['trip_id' => $trip->id, 'day_number' => (int) $dayPayload['day_number']],
                    [
                        'title' => $dayPayload['title'] ?? null,
                        'description' => $dayPayload['description'] ?? null,
                        'hotel_id' => $dayPayload['hotel_id'] ?? null,
                    ]
                );

                $sentActivityIds = [];

                foreach (($dayPayload['activities'] ?? []) as $activityPayload) {
                    if (empty($activityPayload['activity_id'])) {
                        continue;
                    }

                    $activity = DayActivity::query()->updateOrCreate(
                        [
                            'id' => $activityPayload['id'] ?? null,
                            'trip_day_id' => $tripDay->id,
                        ],
                        [
                            'activity_id' => (int) $activityPayload['activity_id'],
                            'start_time' => $activityPayload['start_time'] ?? null,
                            'end_time' => $activityPayload['end_time'] ?? null,
                            'notes' => $activityPayload['notes'] ?? null,
                        ]
                    );

                    $sentActivityIds[] = $activity->id;
                }

                if (! empty($sentActivityIds)) {
                    $tripDay->activities()->whereNotIn('id', $sentActivityIds)->delete();
                } else {
                    $tripDay->activities()->delete();
                }
            }
        });

        return redirect()->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'days'])
            ->with('success', 'Days & activities saved successfully.');
    }

    public function savePackages(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'packages' => 'nullable|array',
            'packages.*.id' => 'nullable|exists:trip_packages,id',
            'packages.*.name' => 'nullable|string|max:255',
            'packages.*.price' => 'nullable|numeric|min:0',
            'packages.*.includes' => 'nullable|string',
            'packages.*.excludes' => 'nullable|string',
            'packages.*.highlights' => 'nullable|string',
            'packages.*.hotels' => 'nullable|array',
            'packages.*.hotels.*.hotel_id' => 'nullable|exists:hotels,id',
            'packages.*.hotels.*.room_type' => 'nullable|string|max:255',
            'packages.*.hotels.*.meal_plan' => 'nullable|string|max:255',
            'packages.*.hotels.*.amenities' => 'nullable|string',
        ]);

        DB::transaction(function () use ($trip, $validated) {
            $keepPackageIds = [];

            foreach (($validated['packages'] ?? []) as $packagePayload) {
                if (blank($packagePayload['name'] ?? null) && blank($packagePayload['price'] ?? null)) {
                    continue;
                }

                $package = TripPackage::query()->updateOrCreate(
                    [
                        'id' => $packagePayload['id'] ?? null,
                        'trip_id' => $trip->id,
                    ],
                    [
                        'name' => $packagePayload['name'] ?? 'Package',
                        'price' => $packagePayload['price'] ?? 0,
                    ]
                );

                $keepPackageIds[] = $package->id;

                $package->includes()->delete();
                collect(explode(PHP_EOL, (string) ($packagePayload['includes'] ?? '')))
                    ->map(fn ($line) => trim($line))
                    ->filter()
                    ->each(fn ($line) => TripInclude::create(['trip_package_id' => $package->id, 'content' => $line]));

                $package->excludes()->delete();
                collect(explode(PHP_EOL, (string) ($packagePayload['excludes'] ?? '')))
                    ->map(fn ($line) => trim($line))
                    ->filter()
                    ->each(fn ($line) => TripExclude::create(['trip_package_id' => $package->id, 'content' => $line]));

                $package->highlights()->delete();
                collect(explode(PHP_EOL, (string) ($packagePayload['highlights'] ?? '')))
                    ->map(fn ($line) => trim($line))
                    ->filter()
                    ->each(fn ($line) => TripHighlight::create([
                        'trip_package_id' => $package->id,
                        'title' => Str::limit($line, 255),
                        'description' => $line,
                    ]));

                $package->packageHotels()->delete();
                foreach (($packagePayload['hotels'] ?? []) as $hotelPayload) {
                    if (empty($hotelPayload['hotel_id'])) {
                        continue;
                    }

                    TripPackageHotel::create([
                        'trip_package_id' => $package->id,
                        'hotel_id' => (int) $hotelPayload['hotel_id'],
                        'room_type' => $hotelPayload['room_type'] ?? null,
                        'meal_plan' => $hotelPayload['meal_plan'] ?? null,
                        'amenities' => collect(explode(',', (string) ($hotelPayload['amenities'] ?? '')))->map(fn ($item) => trim($item))->filter()->values()->all(),
                    ]);
                }
            }

            $trip->packages()->whereNotIn('id', $keepPackageIds ?: [0])->delete();
        });

        return redirect()->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'packages'])
            ->with('success', 'Packages saved successfully.');
    }

    public function saveSchedules(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'schedules' => 'nullable|array',
            'schedules.*.id' => 'nullable|exists:trip_schedules,id',
            'schedules.*.start_date' => 'required|date',
            'schedules.*.end_date' => 'required|date',
            'schedules.*.booking_deadline' => 'nullable|date',
            'schedules.*.available_seats' => 'nullable|integer|min:0',
            'schedules.*.price_modifier' => 'nullable|numeric',
            'schedules.*.status' => 'nullable|string|in:available,full,cancelled',
        ]);

        DB::transaction(function () use ($trip, $validated) {
            $keepIds = [];

            foreach (($validated['schedules'] ?? []) as $schedulePayload) {
                $schedule = TripSchedule::query()->updateOrCreate(
                    ['id' => $schedulePayload['id'] ?? null, 'trip_id' => $trip->id],
                    [
                        'start_date' => $schedulePayload['start_date'],
                        'end_date' => $schedulePayload['end_date'],
                        'booking_deadline' => $schedulePayload['booking_deadline'] ?? null,
                        'available_seats' => $schedulePayload['available_seats'] ?? null,
                        'price_modifier' => $schedulePayload['price_modifier'] ?? 0,
                        'status' => $schedulePayload['status'] ?? 'available',
                    ]
                );

                $keepIds[] = $schedule->id;
            }

            $trip->schedules()->whereNotIn('id', $keepIds ?: [0])->delete();
        });

        return redirect()->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'schedules'])
            ->with('success', 'Schedules saved successfully.');
    }

    public function saveImages(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'cover_image_path' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*.id' => 'nullable|exists:trip_images,id',
            'images.*.image_path' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($trip, $validated) {
            $trip->images()->delete();

            if (! empty($validated['cover_image_path'])) {
                TripImage::create([
                    'trip_id' => $trip->id,
                    'image_path' => $validated['cover_image_path'],
                    'is_cover' => true,
                ]);
            }

            foreach (($validated['images'] ?? []) as $imagePayload) {
                if (blank($imagePayload['image_path'] ?? null)) {
                    continue;
                }

                TripImage::create([
                    'trip_id' => $trip->id,
                    'image_path' => $imagePayload['image_path'],
                    'is_cover' => false,
                ]);
            }
        });

        return redirect()->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'images'])
            ->with('success', 'Images saved successfully.');
    }

    protected function nextUniqueSlug(string $slugBase): string
    {
        $slug = $slugBase ?: 'ai-trip';
        $counter = 1;

        while (Trip::query()->where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function bootstrapPackageFromAiPlan(Trip $trip): void
    {
        if ($trip->packages()->exists()) {
            return;
        }

        $package = TripPackage::create([
            'trip_id' => $trip->id,
            'name' => 'Standard Package',
            'price' => 0,
        ]);

        $hotelIds = $trip->days()->whereNotNull('hotel_id')->pluck('hotel_id')->unique();
        foreach ($hotelIds as $hotelId) {
            TripPackageHotel::create([
                'trip_package_id' => $package->id,
                'hotel_id' => $hotelId,
            ]);
        }
    }
}
