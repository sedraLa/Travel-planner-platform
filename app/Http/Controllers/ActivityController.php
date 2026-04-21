<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use App\Services\GeocodingService;
use App\Models\ActivityHighlight;
use App\Http\Requests\ActivityHighlightRequest;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Activity::with('destination');
        $user = auth()->user();
        $user->load('favoriteActivities');

        if ($user->role !== 'admin') {
        $query->where('availability', 'Available');
    }
    
        // Keyword search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('destination', fn($destinationQuery) => $destinationQuery->where('name', 'like', "%{$search}%"));
            });
        }
    
        // Filters
        if ($request->filled('availability') && $user->role === 'admin') {
         $query->where('availability', $request->availability);
}
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }
        if ($request->filled('guide_language')) {
            $query->where('guide_language', $request->guide_language);
        }
        if ($request->filled('requires_booking')) {
            $query->where('requires_booking', $request->requires_booking);
        }
        if ($request->filled('family_friendly')) {
            $query->where('family_friendly', $request->family_friendly);
        }
        if ($request->filled('pets_allowed')) {
            $query->where('pets_allowed', $request->pets_allowed);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        $selectedDestination = null;
        if ($request->filled('destination_id')) {
            $selectedDestination = Destination::find($request->destination_id);
        }
    
        $activities = $query->paginate(6);
    
    
        return view('activities.index', compact('activities','selectedDestination'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $destinations = Destination::all();
        return view('activities.create', compact('destinations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActivityRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity=Activity::create($data);

        if ($request->has('highlights')) {
        foreach ($request->highlights as $highlightTitle) {
            if (!empty($highlightTitle)) {
                // استخدام العلاقة لإنشاء السجل وربطه بالـ activity_id تلقائياً
                $activity->highlights()->create([
                    'title' => $highlightTitle
                ]);
            }
        }
    }

        return redirect()->route('activities.index')->with('success', 'Activity created successfully!');
    }



  public function show($id, GeocodingService $geo)
{
    $activity = Activity::with(['destination','highlights'])->findOrFail($id);
    $hasPaidReservation = $activity->reservations()
        ->where('user_id', auth()->id())
        ->where('status', 'confirmed')
        ->exists();

    
    $addressToSearch = $activity->address ?? optional($activity->destination)->name;
    $coords = null;

    // محاولة جلب الإحداثيات فقط إذا كان هناك عنوان
    if ($addressToSearch) {
        $coords = $geo->geocodeAddress($addressToSearch);
    }

    // إذا كانت الإحداثيات فارغة أو غير صالحة، نضمن أنها null تماماً
    if (!$coords || empty($coords['latitude']) || empty($coords['longitude'])) {
        $coords = null;
    }

    return view('activities.show', compact('activity', 'hasPaidReservation', 'coords'));
}

    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
{
    $activity = Activity::with(['destination','highlights'])->findOrFail($id);
    $destinations = Destination::all();
    return view('activities.edit', compact('activity', 'destinations'));
}

public function update(ActivityRequest $request, $id)
{
    $activity = Activity::findOrFail($id);
    $data = $request->validated();

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('activities', 'public');
    }
    $activity->update($data);
    $activity->highlights()->delete();


    if ($request->has('highlights')) {
        foreach ($request->highlights as $highlightTitle) {
            if (!empty($highlightTitle)) {
                $activity->highlights()->create([
                    'title' => $highlightTitle
                ]);
            }
        }
    }

    return redirect()->route('activities.index')->with('success', 'Activity updated successfully!');
}

public function destroy($id)
{
    $activity = Activity::findOrFail($id);


     if ($activity->reservations()->exists()) {
        return redirect()->back()->with('error', 'Cannot delete activity with reservations.');
    }
    if ($activity->image && \Storage::disk('public')->exists($activity->image)) {
        \Storage::disk('public')->delete($activity->image);
    }
    $activity->delete();

    return redirect()->route('activities.index')->with('success', 'Activity deleted successfully!');
}
}
