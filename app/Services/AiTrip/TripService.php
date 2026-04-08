<?php

namespace App\Services\AiTrip;

use App\Models\DayActivity;
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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TripService
{
    public function __construct(protected GroqTripPlannerService $groqService)
    {
    }

    public function createFromAi(array $payload): ?Trip
    {
        $payload['destination_ids'] = array_values(array_unique(array_map('intval', $payload['destination_ids'])));
        $payload['categories'] = array_values(array_unique($payload['categories']));
        $language = $payload['language'] ?? 'en';

        $plan = $this->groqService->generateTripPlan($payload, $language);

        if (! $plan) {
            return null;
        }

        return DB::transaction(function () use ($payload, $plan) {
            $name = Str::limit($plan['trip_name'] ?: $payload['description'], 120, '');
            $slugBase = Str::slug($name ?: 'ai-trip');
            $primaryDestinationId = (int) $payload['destination_ids'][0];

            $trip = Trip::create([
                'destination_id' => $primaryDestinationId,
                'name' => $name,
                'slug' => $this->nextUniqueSlug($slugBase),
                'description' => $plan['trip_description'] ?? null,
                'duration_days' => (int) $payload['duration'],
                'category' => implode(',', $payload['categories']),
                'max_participants' => (int) $payload['max_participants'],
                'meeting_point_description' => null,
                'meeting_point_address' => null,
                'is_ai_generated' => true,
                'ai_prompt' => $payload['description'],
                'status' => 'draft',
            ]);

            $this->syncDestinations($trip, $payload['destination_ids']);
            $this->createDaysAndActivities($trip, $plan['days'] ?? []);
            $this->bootstrapPackageFromAiPlan($trip);

            return $trip;
        });
    }

    public function saveBasics(Trip $trip, array $payload): void
    {
        $destinationIds = array_values(array_unique(array_map('intval', $payload['destination_ids'])));
        $primaryDestinationId = (int) $payload['destination_id'];

        if (! in_array($primaryDestinationId, $destinationIds, true)) {
            array_unshift($destinationIds, $primaryDestinationId);
        }

        $destinationIds = array_values(array_unique([$primaryDestinationId, ...$destinationIds]));

        DB::transaction(function () use ($trip, $payload, $destinationIds, $primaryDestinationId) {
            $trip->update(
                collect($payload)
                    ->except('destination_ids')
                    ->put('destination_id', $primaryDestinationId)
                    ->put('status', 'draft')
                    ->all()
            );

            $this->syncDestinations($trip, $destinationIds);
        });
    }

    public function saveDaysActivities(Trip $trip, array $payload): void
    {
        DB::transaction(function () use ($trip, $payload) {
            foreach ($payload['days'] as $dayPayload) {
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
                        $this->dayActivityAttributes($tripDay->id, $activityPayload)
                    );

                    $sentActivityIds[] = $activity->id;
                }

                if (! empty($sentActivityIds)) {
                    $tripDay->activities()->whereNotIn('id', $sentActivityIds)->delete();
                } else {
                    $tripDay->activities()->delete();
                }
            }

            $canonicalHotelIds = $this->canonicalHotelIdsFromDays($trip->fresh('days'));
            $trip->packages()->with('packageHotels')->get()->each(function (TripPackage $package) use ($canonicalHotelIds) {
                $payload = $package->packageHotels
                    ->map(fn (TripPackageHotel $packageHotel) => [
                        'hotel_id' => $packageHotel->hotel_id,
                        'room_type' => $packageHotel->room_type,
                        'meal_plan' => $packageHotel->meal_plan,
                        'amenities' => is_array($packageHotel->amenities) ? implode(', ', $packageHotel->amenities) : '',
                        'notes' => $packageHotel->notes,
                    ])
                    ->values()
                    ->all();

                $this->syncPackageHotelsFromDays($package, $payload, $canonicalHotelIds);
            });
        });
    }

    public function savePackages(Trip $trip, array $payload): void
    {
        DB::transaction(function () use ($trip, $payload) {
            $keepPackageIds = [];
            $canonicalHotelIds = $this->canonicalHotelIdsFromDays($trip);

            foreach (($payload['packages'] ?? []) as $packagePayload) {
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
                $this->syncPackageTextBlocks($package, $packagePayload);
                $this->syncPackageHotelsFromDays($package, $packagePayload['hotels'] ?? [], $canonicalHotelIds);
            }

            $trip->packages()->whereNotIn('id', $keepPackageIds ?: [0])->delete();
        });
    }

    public function saveSchedules(Trip $trip, array $payload): void
    {
        DB::transaction(function () use ($trip, $payload) {
            $keepIds = [];

            foreach (($payload['schedules'] ?? []) as $schedulePayload) {
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
    }

    public function saveImages(Trip $trip, array $payload, ?UploadedFile $coverImageFile = null, array $imageFiles = []): void
    {
        DB::transaction(function () use ($trip, $payload, $coverImageFile, $imageFiles) {
            $trip->images()->delete();

            $coverImagePath = $payload['cover_existing_path'] ?? null;
            if ($coverImageFile) {
                $coverImagePath = '/storage/' . Storage::disk('public')->put('trips', $coverImageFile);
            }

            if (! empty($coverImagePath)) {
                TripImage::create([
                    'trip_id' => $trip->id,
                    'image_path' => $coverImagePath,
                    'is_cover' => true,
                ]);
            }

            foreach (($payload['images'] ?? []) as $index => $imagePayload) {
                $imagePath = $imagePayload['existing_path'] ?? null;

                if (($imageFiles[$index]['image_file'] ?? null) instanceof UploadedFile) {
                    $imagePath = '/storage/' . Storage::disk('public')->put('trips', $imageFiles[$index]['image_file']);
                }

                if (blank($imagePath)) {
                    continue;
                }

                TripImage::create([
                    'trip_id' => $trip->id,
                    'image_path' => $imagePath,
                    'is_cover' => false,
                ]);
            }
        });
    }

    protected function createDaysAndActivities(Trip $trip, array $days): void
    {
        foreach ($days as $day) {
            $tripDay = TripDay::create([
                'trip_id' => $trip->id,
                'day_number' => (int) $day['day_number'],
                'title' => $day['title'],
                'description' => $day['description'],
                'highlights' => [],
                'hotel_id' => $day['hotel_id'] ?? null,
            ]);

            foreach ($day['activities'] as $activity) {
                DayActivity::query()->updateOrCreate(
                    [
                        'trip_day_id' => $tripDay->id,
                        'activity_id' => (int) ($activity['activity_id'] ?? 0),
                    ],
                    $this->dayActivityAttributes($tripDay->id, $activity)
                );
            }
        }
    }

    protected function syncPackageTextBlocks(TripPackage $package, array $payload): void
    {
        $package->includes()->delete();
        collect($this->normalizeRepeaterInput($payload['includes'] ?? []))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(fn ($line) => TripInclude::create(['trip_package_id' => $package->id, 'content' => $line]));

        $package->excludes()->delete();
        collect($this->normalizeRepeaterInput($payload['excludes'] ?? []))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(fn ($line) => TripExclude::create(['trip_package_id' => $package->id, 'content' => $line]));

        $package->highlights()->delete();
        collect($this->normalizeRepeaterInput($payload['highlights'] ?? []))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(fn ($line) => TripHighlight::create([
                'trip_package_id' => $package->id,
                'title' => Str::limit($line, 255),
                'description' => $line,
            ]));
    }

    protected function syncDestinations(Trip $trip, array $destinationIds): void
    {
        $trip->itineraryDestinations()->sync(
            collect($destinationIds)->values()->mapWithKeys(fn (int $destinationId, int $index) => [
                $destinationId => ['sort_order' => $index + 1],
            ])->all()
        );
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

        $hotelIds = $this->canonicalHotelIdsFromDays($trip);
        foreach ($hotelIds as $hotelId) {
            TripPackageHotel::create([
                'trip_package_id' => $package->id,
                'hotel_id' => $hotelId,
            ]);
        }
    }

    protected function canonicalHotelIdsFromDays(Trip $trip): array
    {
        return $trip->days()
            ->orderBy('day_number')
            ->whereNotNull('hotel_id')
            ->pluck('hotel_id')
            ->map(fn ($hotelId) => (int) $hotelId)
            ->unique()
            ->values()
            ->all();
    }

    protected function syncPackageHotelsFromDays(TripPackage $package, array $hotelPayloads, array $canonicalHotelIds): void
    {
        $payloadByHotelId = collect($hotelPayloads)
            ->filter(fn ($hotelPayload) => ! empty($hotelPayload['hotel_id']))
            ->keyBy(fn ($hotelPayload) => (int) $hotelPayload['hotel_id']);

        $payloadByIndex = collect($hotelPayloads)->values();

        $package->packageHotels()->delete();

        foreach ($canonicalHotelIds as $index => $hotelId) {
            $matchedPayload = $payloadByHotelId->get($hotelId) ?? $payloadByIndex->get($index, []);

            TripPackageHotel::create([
                'trip_package_id' => $package->id,
                'hotel_id' => $hotelId,
                'room_type' => $matchedPayload['room_type'] ?? null,
                'meal_plan' => $matchedPayload['meal_plan'] ?? null,
                'amenities' => collect(explode(',', (string) ($matchedPayload['amenities'] ?? '')))
                    ->map(fn ($item) => trim($item))
                    ->filter()
                    ->values()
                    ->all(),
                'notes' => $matchedPayload['notes'] ?? null,
            ]);
        }
    }

    protected function dayActivityAttributes(int $tripDayId, array $activityPayload): array
    {
        return [
            'trip_day_id' => $tripDayId,
            'activity_id' => (int) ($activityPayload['activity_id'] ?? 0),
            'start_time' => $activityPayload['start_time'] ?? null,
            'end_time' => $activityPayload['end_time'] ?? null,
            'notes' => $activityPayload['notes'] ?? null,
        ];
    }

    protected function normalizeRepeaterInput(mixed $value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if (is_string($value)) {
            return explode(PHP_EOL, $value);
        }

        return [];
    }
}
