<?php

namespace App\Services\AiTrip;

use App\Models\DayActivity;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\TripPackage;
use App\Models\TripPackageHotel;
use Illuminate\Support\Facades\DB;

class TripDaysService
{
    public function __construct(protected TripPackagesService $tripPackagesService)
    {
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

            $canonicalHotelIds = $this->tripPackagesService->canonicalHotelIdsFromDays($trip->fresh('days'));
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

                $this->tripPackagesService->syncPackageHotelsFromDays($package, $payload, $canonicalHotelIds);
            });
        });
    }

    public function createDaysAndActivities(Trip $trip, array $days): void
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

    public function dayActivityAttributes(int $tripDayId, array $activityPayload): array
    {
        return [
            'trip_day_id' => $tripDayId,
            'activity_id' => (int) ($activityPayload['activity_id'] ?? 0),
            'start_time' => $activityPayload['start_time'] ?? null,
            'end_time' => $activityPayload['end_time'] ?? null,
            'notes' => $activityPayload['notes'] ?? null,
        ];
    }
}
