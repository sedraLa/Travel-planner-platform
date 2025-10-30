<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Http\Requests\ActivityRequest;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = Activity::with('destination');

    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where('name', 'like', "%{$search}%")
              ->orWhereHas('destination', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
    }

    $activities = $query->get(); // جلب النشاطات بناءً على البحث
    return view('activities.index', compact('activities'));
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

        Activity::create($data);

        return redirect()->route('activities.index')->with('success', 'Activity created successfully!');
    }

    /**
     * Display the specified resource.
     */
     /*  public function show(Activity $activity) // استخدام Route Model Binding
    {
        // تحميل الوجهة المرتبطة بالنشاط لتجنب استعلامات إضافية
        $activity->load('destination');

        // إرجاع صفحة العرض مع تمرير بيانات النشاط
        return view('activities.show', compact('activity'));
    }
*/
    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
{
    $activity = Activity::findOrFail($id);
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

    return redirect()->route('activities.index')->with('success', 'Activity updated successfully!');
}

public function destroy($id)
{
    $activity = Activity::findOrFail($id);
    if ($activity->image && \Storage::disk('public')->exists($activity->image)) {
        \Storage::disk('public')->delete($activity->image);
    }
    $activity->delete();

    return redirect()->route('activities.index')->with('success', 'Activity deleted successfully!');
}
}
