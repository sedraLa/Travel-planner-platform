<?php

namespace App\Services\AiTrip;

use App\Models\Trip;
use Illuminate\Http\UploadedFile;

class TripService
{
    public function __construct(
        protected TripCreationService $tripCreationService,
        protected TripBasicsService $tripBasicsService,
        protected TripDaysService $tripDaysService,
        protected TripPackagesService $tripPackagesService,
        protected TripMediaService $tripMediaService,
        protected TripScheduleService $tripScheduleService,
        protected TripStateService $tripStateService,
        protected TripSlugService $tripSlugService
    )
    {
    }

    public function createFromAi(array $payload): ?Trip
    {
        return $this->tripCreationService->createFromAi($payload);
    }

    public function saveBasics(Trip $trip, array $payload): void
    {
        $this->tripBasicsService->saveBasics($trip, $payload);
    }

    public function saveDaysActivities(Trip $trip, array $payload): void
    {
        $this->tripDaysService->saveDaysActivities($trip, $payload);
    }

    public function savePackages(Trip $trip, array $payload): void
    {
        $this->tripPackagesService->savePackages($trip, $payload);
    }

    public function saveSchedules(Trip $trip, array $payload): void
    {
        $this->tripScheduleService->saveSchedules($trip, $payload);
    }

    public function saveImages(Trip $trip, array $payload, ?UploadedFile $coverImageFile = null, array $imageFiles = []): void
    {
        $this->tripMediaService->saveImages($trip, $payload, $coverImageFile, $imageFiles);
    }

    public function confirmOverview(Trip $trip): void
    {
        $this->tripStateService->confirmOverview($trip);
    }
}
