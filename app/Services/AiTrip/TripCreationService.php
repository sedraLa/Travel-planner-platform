<?php

namespace App\Services\AiTrip;

use App\Models\DayActivity;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\TripPackage;
use App\Models\TripPackageHotel;
use App\Services\GroqTripPlannerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TripCreationService
{
    public function __construct(
        protected GroqTripPlannerService $groqService,
        protected TripBasicsService $tripBasicsService,
        protected TripDaysService $tripDaysService,
        protected TripSlugService $tripSlugService,
        protected TripPackagesService $tripPackagesService
    )
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
                'slug' => $this->tripSlugService->nextUniqueSlug($slugBase),
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

            $this->tripBasicsService->syncDestinations($trip, $payload['destination_ids']);
            $this->tripDaysService->createDaysAndActivities($trip, $plan['days'] ?? []);
            $this->bootstrapPackageFromAiPlan($trip);
            return $trip;
        });
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

        $hotelIds = $this->tripPackagesService->canonicalHotelIdsFromDays($trip);
        foreach ($hotelIds as $hotelId) {
            TripPackageHotel::create([
                'trip_package_id' => $package->id,
                'hotel_id' => $hotelId,
            ]);
        }
    }
}
