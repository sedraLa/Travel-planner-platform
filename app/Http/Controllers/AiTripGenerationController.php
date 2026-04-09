<?php

namespace App\Http\Controllers;

use App\Http\Requests\AiTripGenerateRequest;
use App\Services\AiTrip\TripService;

class AiTripGenerationController extends Controller
{
    public function __construct(protected TripService $tripService)
    {
    }

    public function generate(AiTripGenerateRequest $request)
    {
        $trip = $this->tripService->createFromAi($request->validated());

        if (! $trip) {
            return back()->withErrors(['api_error' => 'Failed to generate trip from Groq API.']);
        }

        return redirect()->route('trip.complete.edit', $trip)
            ->with('success', 'Your AI trip has been generated as draft. Complete the remaining details and save each tab.');
    }
}
