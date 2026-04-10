<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Jobs\TripStaffing\ProcessNextGuideInChainJob;
use App\Models\GuideAssignment;
use App\Models\GuideRequest;
use App\Services\TripStaffing\TripStaffingCoordinator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuideRequestResponseController extends Controller
{
    public function index(): View
    {
        $guide = auth()->user()?->guide;

        abort_unless($guide, 403);

        $requests = GuideRequest::query()
            ->with(['trip.primaryDestination', 'trip.schedules', 'trip.days.activities.activity'])
            ->where('guide_id', $guide->id)
            ->latest()
            ->get();

        return view('guide.booking-requests', compact('requests'));
    }

    public function accept(GuideRequest $guideRequest, TripStaffingCoordinator $coordinator): RedirectResponse
    {
        $guide = auth()->user()?->guide;

        abort_unless($guide && $guideRequest->guide_id === $guide->id, 403);

        if (! $coordinator->acceptGuideRequest($guideRequest)) {
            return back()->withErrors('Guide request is no longer pending.');
        }

        return back()->with('success', 'Guide request accepted successfully.');
    }

    public function reject(GuideRequest $guideRequest, TripStaffingCoordinator $coordinator): RedirectResponse
    {
        $guide = auth()->user()?->guide;

        abort_unless($guide && $guideRequest->guide_id === $guide->id, 403);
        abort_unless($guideRequest->status === 'pending', 422, 'Request is no longer pending.');

        $coordinator->rejectGuideRequest($guideRequest);

        $rankedGuideIds = $guideRequest->trip->ranked_guide_ids ?? [];
        $currentIndex = array_search($guide->id, $rankedGuideIds, true);

        ProcessNextGuideInChainJob::dispatchSync(
            $guideRequest->trip_id,
            $rankedGuideIds,
            $currentIndex === false ? ($guideRequest->chain_index + 1) : $currentIndex + 1
        );

        return back()->with('success', 'Guide request rejected.');
    }

    public function upcomingTrips(): View
    {
        $guide = auth()->user()?->guide;

        abort_unless($guide, 403);

        $assignments = GuideAssignment::query()
            ->with(['trip.primaryDestination', 'trip.schedules', 'trip.days.activities.activity'])
            ->where('guide_id', $guide->id)
            ->where('status', 'assigned')
            ->latest()
            ->get();

        $today = Carbon::today();
        $upcomingAssignments = $assignments->filter(function (GuideAssignment $assignment) use ($today) {
            $latestEndDate = $assignment->trip?->schedules?->max('end_date');

            return ! $latestEndDate || Carbon::parse($latestEndDate)->endOfDay()->gte($today);
        })->values();

        return view('guide.upcoming-trips', ['assignments' => $upcomingAssignments]);
    }

    public function completedTrips(): View
    {
        $guide = auth()->user()?->guide;

        abort_unless($guide, 403);

        $assignments = GuideAssignment::query()
            ->with(['trip.primaryDestination', 'trip.schedules', 'trip.days.activities.activity'])
            ->where('guide_id', $guide->id)
            ->where('status', 'assigned')
            ->latest()
            ->get();

        $today = Carbon::today();
        $completedAssignments = $assignments->filter(function (GuideAssignment $assignment) use ($today) {
            $latestEndDate = $assignment->trip?->schedules?->max('end_date');

            return $latestEndDate && Carbon::parse($latestEndDate)->endOfDay()->lt($today);
        })->values();

        return view('guide.completed-trips', ['assignments' => $completedAssignments]);
    }
}
