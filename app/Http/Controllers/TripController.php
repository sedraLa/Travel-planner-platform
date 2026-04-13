<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Activity;
use App\Models\Guide;

class TripController extends Controller
{
    public function dashboard()
    {
        $systemGuides = Guide::with('user')
            ->where('status', 'approved')
            ->count();
        $guidesRequests = Guide::with('user')
            ->where('status', 'pending')
            ->count();
        $activities = Activity::with('destinations')->count();
        $publishedTrips = Trip::where('status','published')->count();
        $draftTrips = Trip::where('status','draft')->count();

        return view('trips.dashboard', compact('systemGuides', 'guidesRequests', 'activities','publishedTrips','draftTrips'));
    }

    public function index()
    {
        $trips = Trip::with('destination')
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('ai_prompt', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(request('destination'), function ($query, $destination) {
                $query->whereHas('destination', fn ($destinationQuery) => $destinationQuery->where('name', 'like', "%{$destination}%"));
            })
            ->latest()
            ->get();

        return view('trips.index', compact('trips'));
    }

    public function view()
    {
        return view('trips.view');
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();

        return redirect()
            ->back()
            ->with('success', 'Trip deleted successfully');
    }
}
