<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuideAvailabilityRequest;
use App\Models\GuideAvailability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuideAvailabilityController extends Controller
{





public function store(GuideAvailabilityRequest $request)
    {
        $guide = $request->user()->guide;
        $validated = $request->validated();

        $hasOverlap = $guide->availabilities()
            ->whereDate('date', $validated['date'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

       if ($hasOverlap) {
      return back()->withErrors(['date' => 'This time overlaps with an existing availability.']);
   }

        $availability = $guide->availabilities()->create($validated);

        return back()->with('success', 'Saved availabilty');
    }


    



}