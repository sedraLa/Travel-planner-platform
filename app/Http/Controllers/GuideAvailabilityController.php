<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Carbon\Carbon;

class GuideAvailabilityController extends Controller
{
    public function show($guideId)
    {
        $guide = Guide::with(['assignments.trip.schedules'])
            ->findOrFail($guideId);

        $dates = $guide->assignments
            ->flatMap(function ($assignment) {

                if (!$assignment->trip || !$assignment->trip->schedules) {
                    return [];
                }

                return $assignment->trip->schedules;
            })
            ->flatMap(function ($schedule) {

                if (!$schedule->start_date || !$schedule->end_date) {
                    return [];
                }

                $start = Carbon::parse($schedule->start_date);
                $end   = Carbon::parse($schedule->end_date);

                $range = [];

                while ($start->lte($end)) {
                    $range[] = $start->toDateString();
                    $start->addDay();
                }

                return $range;
            })
            ->unique()
            ->values();

        return response()->json([
            'guide_id' => $guide->id,
            'dates' => $dates
        ]);
    }
}