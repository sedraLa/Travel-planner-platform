<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuideAssignmentRequest;
use App\Http\Requests\GuideAvailabilityRequest;
use App\Models\Guide;
use App\Models\GuideAssignment;
use App\Models\GuideAvailability;
use App\Models\Trip;
use App\Notifications\GuideTripAssignmentRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuideAssignmentController extends Controller
{
    private const MAX_ASSIGNABLE_DAYS = 6;

    public function store(GuideAssignmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $trip = Trip::findOrFail($validated['trip_id']);
        $guide = Guide::with('user')->findOrFail($validated['guide_id']);


        $hasConflict = GuideAvailability::where('guide_id', $guide->id)
      ->whereBetween('date', [$startDate, $startDate->copy()->addDays($requiredDays - 1)])
      ->exists();

    if ($hasConflict) {
    return back()->withErrors('You already have assignments during these dates.');
     }

        $requiredDays = max(1, (int) ($trip->duration_days ?? 1));
        $currentAssignedDays = $this->getCurrentAssignedDays($guide);

        if (($currentAssignedDays + $requiredDays) > self::MAX_ASSIGNABLE_DAYS) {
            return back()->withErrors([
                'guide_id' => 'This guide already has more than 6 duty days and cannot receive a new trip request.',
            ]);
        }

        $existingPending = GuideAssignment::where('trip_id', $trip->id)
            ->where('guide_id', $guide->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return back()->withErrors([
                'guide_id' => 'A pending request already exists for this guide and trip.',
            ]);
        }

        $assignment = GuideAssignment::create([
            'trip_id' => $trip->id,
            'guide_id' => $guide->id,
            'status' => 'pending',
        ]);

        if ($guide->user) {
            $guide->user->notify(new GuideTripAssignmentRequestNotification($assignment));
        }

        return back()->with('success', 'Trip request sent to guide successfully.');
    }


    /////////////////////////
    public function myRequests(): \Illuminate\View\View
    {
        $guide = auth()->user()->guide;

        $requests = GuideAssignment::with(['trip.destination'])
            ->where('guide_id', $guide->id)
            ->latest()
            ->get();

        return view('guide.assignment-requests', compact('requests'));
    }
///////////////////////////////////
    public function accept(GuideAssignment $guideAssignment): RedirectResponse
    {
        $guide = auth()->user()->guide;

       if ($guideAssignment->guide_id !== $guide->id) {
         abort(403, 'You are not authorized to accept this request.');
          }

      if ($guideAssignment->status !== 'pending') {
       abort(422, 'Request is no longer pending.');
       }

        $guideAssignment->load('trip.schedules');

        $requiredDays = max(1, (int) ($guideAssignment->trip?->duration_days ?? 1));
        $currentAssignedDays = $this->getCurrentAssignedDays($guide);

        if (($currentAssignedDays + $requiredDays) > self::MAX_ASSIGNABLE_DAYS) {
            return back()->withErrors('Accepting this request exceeds the 6-day duty limit.');
        }

        DB::transaction(function () use ($guideAssignment, $guide, $requiredDays) {
            $guideAssignment->update(['status' => 'accepted']);

            GuideAssignment::where('trip_id', $guideAssignment->trip_id)
                ->where('id', '!=', $guideAssignment->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            $tripStartDate = optional(
                $guideAssignment->trip->schedules
                    ->where('start_date', '>=', now()->toDateString())
                    ->sortBy('start_date')
                    ->first()
            )->start_date;

            $startDate = $tripStartDate
                ? Carbon::parse($tripStartDate)
                : now()->startOfDay();


                $hasConflict = GuideAvailability::where('guide_id', $guide->id)
               ->whereBetween('date', [$startDate->toDateString(), $startDate->copy()->addDays($requiredDays - 1)->toDateString()])
              ->exists();

                   if ($hasConflict) {
                       return back()->withErrors('You already have assignments during these dates.');
                         }

            for ($day = 0; $day < $requiredDays; $day++) {
                $date = $startDate->copy()->addDays($day)->toDateString();

                 GuideAvailability::create([
                'guide_id' => $guide->id,
                'date' => $date,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
            }
        });

        return back()->with('success', 'Trip request accepted and your availability was updated.');
    }
//////////////////////////////////////////
    public function reject(GuideAssignment $guideAssignment): RedirectResponse
    {
        $guide = auth()->user()->guide;

        
       if ($guideAssignment->guide_id !== $guide->id) {
         abort(403, 'You are not authorized to accept this request.');
          }

      if ($guideAssignment->status !== 'pending') {
       abort(422, 'Request is no longer pending.');
       }

        $guideAssignment->update(['status' => 'rejected']);

        return back()->with('success', 'Trip request rejected.');
    }
/////////////////////////////////////
  /*public function storeAvailability(GuideAvailabilityRequest $request): RedirectResponse
    {
        $guide = auth()->user()->guide;
        $validated = $request->validated();

        if ((int) $validated['guide_id'] !== (int) $guide->id) {
            abort(403);
        }

        GuideAvailability::updateOrCreate(
            [
                'guide_id' => $guide->id,
                'date' => $validated['date'],
            ],
            [
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]
        );

        return back()->with('success', 'Availability saved successfully.');
    }*/
    ///////////////////////////////////////

    private function getCurrentAssignedDays(Guide $guide): int
    {
        return (int) GuideAssignment::query()
            ->where('guide_id', $guide->id)
            ->where('status', 'accepted')
            ->with('trip:id,duration_days')
            ->get()
            ->sum(fn (GuideAssignment $assignment) => max(1, (int) optional($assignment->trip)->duration_days));
    }
}