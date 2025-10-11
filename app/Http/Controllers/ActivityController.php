<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Http\Requests\ActivityRequest;
use App\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::with('destination')->get(); // جلب كل النشاطات مع الوجهة
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
