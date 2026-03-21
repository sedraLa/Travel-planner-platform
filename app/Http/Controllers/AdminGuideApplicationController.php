<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuideRequest;
use App\Models\Guide;
use App\Models\Specialization;
use App\Services\GuideApplicationStatusService;
use Illuminate\Http\Request;

class AdminGuideApplicationController extends Controller
{
    /**
     * show application requests
     */
    public function index(Request $request)
    {
        $specializations = Specialization::all();
        $query = Guide::with('user', 'specializations')->where('status', 'pending');

        if ($request->filled('search')) {
            $term = $request->search;

            $query->where(function ($q) use ($term) {
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%$term%")
                        ->orWhere('last_name', 'like', "%$term%")
                        ->orWhere('email', 'like', "%$term%"))
                    ->orWhere('languages', 'like', "%$term%")
                    ->orWhereHas('specializations', fn ($s) =>
                        $s->where('name', 'like', "%$term%"));
            });
        }

        if ($request->filled('country')) {
            $query->whereHas('user', fn ($u) =>
                $u->where('country', 'like', "%{$request->country}%"));
        }

        if ($request->filled('languages')) {
            $query->where('languages', 'like', "%{$request->languages}%");
        }

        if ($request->filled('tour_leader')) {
            $query->where('is_tour_leader', $request->tour_leader);
        }

        if ($request->filled('specialization')) {
            $query->whereHas('specializations', function ($q) use ($request) {
                $q->where('specializations.id', $request->specialization);
            });
        }

        $guides = $query
            ->orderBy('years_of_experience', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Guide-applications.index', compact('guides', 'specializations'));
    }

    /*
    show request details
    */
    public function show(string $id)
    {
        $guide = Guide::with(['user', 'specializations'])->findOrFail($id);

        return view('Guide-applications.show', compact('guide'));
    }

    /*
    Approve or reject applications
    */
    public function updateStatus(GuideRequest $request, Guide $guide, GuideApplicationStatusService $statusService)
    {
        if (auth()->user()->role != 'admin') {
            abort(403, 'Unauthorized');
        }

        if ($guide->status === 'approved') {
            return redirect()->back()->with('error', 'Approved guides status cannot be changed.');
        }

        $validated = $request->validated();

        if ($validated['status'] === 'pending') {
            return redirect()->back()
                ->with('error', 'Please select a status before confirming.');
        }

        $statusService->updateStatus($guide, $validated['status']);

        if ($validated['status'] === 'rejected') {
            return redirect()->back()->with('success', 'Guide was rejected, email sent, and guide removed.');
        }

        return redirect()->back()->with('success', 'Guide accepted, status updated and email sent successfully.');
    }
}
