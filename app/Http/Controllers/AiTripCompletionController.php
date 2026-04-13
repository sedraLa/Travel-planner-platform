<?php

namespace App\Http\Controllers;

use App\Http\Requests\AiTripBasicsRequest;
use App\Http\Requests\AiTripDaysActivitiesRequest;
use App\Http\Requests\AiTripImagesRequest;
use App\Http\Requests\AiTripPackagesRequest;
use App\Http\Requests\AiTripSchedulesRequest;
use App\Http\Requests\ConfirmTripRequest;
use App\Models\Trip;
use App\Services\AiTrip\TripService;

class AiTripCompletionController extends Controller
{
    public function __construct(protected TripService $tripService)
    {
    }

    public function saveBasics(AiTripBasicsRequest $request, Trip $trip)
    {
        $this->tripService->saveBasics($trip, $request->validated());

        return redirect()
            ->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'basics'])
            ->with('success', 'Basics saved successfully.');
    }

    public function saveDaysActivities(AiTripDaysActivitiesRequest $request, Trip $trip)
    {
        $this->tripService->saveDaysActivities($trip, $request->validated());

        return redirect()
            ->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'days'])
            ->with('success', 'Days & activities saved successfully.');
    }

    public function savePackages(AiTripPackagesRequest $request, Trip $trip)
    {
        $this->tripService->savePackages($trip, $request->validated());

        return redirect()
            ->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'packages'])
            ->with('success', 'Packages saved successfully.');
    }

    public function saveSchedules(AiTripSchedulesRequest $request, Trip $trip)
    {
        $this->tripService->saveSchedules($trip, $request->validated());

        return redirect()
            ->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'schedules'])
            ->with('success', 'Schedules saved successfully.');
    }

    public function saveImages(AiTripImagesRequest $request, Trip $trip)
    {
        $this->tripService->saveImages(
            $trip,
            $request->validated(),
            $request->file('cover_image_file'),
            $request->file('images', [])
        );

        return redirect()
            ->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'images'])
            ->with('success', 'Images saved successfully.');
    }

    public function confirmOverview(ConfirmTripRequest $request, Trip $trip)
    {
        $this->tripService->confirmOverview($trip);
    
        return redirect()
            ->route('trip.complete.edit', ['trip' => $trip, 'tab' => 'overview'])
            ->with('success', 'Trip confirmed. Guide assignment has been started.');
    }

   
}