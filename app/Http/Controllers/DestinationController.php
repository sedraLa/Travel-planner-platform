<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\DestinationImage;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;



class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Destination::with('images');

        //search by name, location(city, country)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('city', 'like', '%' . $searchTerm . '%')
                  ->orWhere('country', 'like', '%' . $searchTerm . '%');
            });
        }

        $destinations = $query->get();


        return view('destinations.index',compact('destinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('destinations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:destinations,name', // التأكد من اسم الوجهة غير مكرر
            'description' => 'nullable',
            'location_details' => 'required',
           // 'weather_info' => 'required',
            'activities' => 'nullable',
            'city' => 'required|string|max:255',
             'country' => 'required|string|max:255',
            // التأكد من أن الصورة الرئيسية هي عدد صحيح
            'images' => 'required|array',
             'images.*' => 'image|mimes:jpeg,png,jpg,gif',
             'primary_image_index' => 'nullable|integer',
        ]);

        // حفظ الوجهة
        $destination = new Destination();
        $destination->name = $request->name;
        $destination->description = $request->description;
        $destination->location_details = $request->location_details;
        //$destination->weather_info = $request->weather_info;
        $destination->activities = $request->activities;
        $destination->city = $request->city;
        $destination->country = $request->country ;

        $destination->save();

        // إذا كانت هناك صور تم تحميلها

        if ($request->hasFile('images')) {
            $images = $request->file('images');


            // حفظ الصور
            foreach ($images as $index => $image) {
                $imagePath = MediaServices::save($image, 'image', 'Destinations');
                $destination->images()->create([
                    'image_url' => $imagePath,
                    'is_primary' => $request->primary_image_index == $index ? true : false, // إذا الصورة هي الرئيسية
                ]);
            }
        }

        // إرجاع إلى صفحة الوجهات مع رسالة نجاح
        return redirect()->route('destination.index')->with('success', 'Destination created successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $destination = Destination::with('images')->findOrFail($id);
        $primaryImage = $destination->images->where('is_primary', true)->first();
        return view('destinations.show', compact('destination', 'primaryImage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    $destination = Destination::findOrFail($id);

    return view('destinations.edit', compact('destination'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    $destination = Destination::findOrFail($id);


    $validatedData = $request->validate([
    'name' => 'required|string|max:255',
    'city' => 'required|string|max:255',
    'country' => 'required|string|max:255',
    'location_details' => 'required|string',
    'description' => 'nullable|string',
    'activities' => 'nullable|string',
    'images' => 'nullable|array',
    'images.*' => 'image|mimes:jpeg,png,jpg,gif',
]);


    // تحديث بيانات الـ destination
    $destination->update($validatedData);

    // التعامل مع رفع الصور الجديدة
    if ($request->hasFile('images')) {
        $images = $request->file('images');
        if (!is_array($images)) {
            $images = [$images]; // ضمان التعامل مع صورة واحدة كمصفوفة
        }

        foreach ($images as $image) {
            $imagePath = MediaServices::save($image, 'image', 'Destinations');
            $destination->images()->create([
                'image_url' => $imagePath,
            ]);
        }
    }



    // إعادة التوجيه بعد التحديث
    return redirect()->route('destination.index')->with('success', 'Destination updated successfully');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $image = DestinationImage::findOrFail($id);

    // تحقق إذا كانت الصورة هي الرئيسية
    if ($image->is_primary) {
        return back()->with('error', 'لا يمكن حذف الصورة الرئيسية. يرجى تعيين صورة أخرى كصورة رئيسية أولاً.');
    }

    // حذف الصورة من التخزين
    Storage::delete('public/' . $image->image_url);

    // حذف السطر من قاعدة البيانات
    $image->delete();

    return back()->with('success', 'تم حذف الصورة بنجاح.');
}


    public function setPrimary($id)
    {
        $image = DestinationImage::findOrFail($id);
        $destination = $image->destination;

        // جعل كل الصور غير رئيسية
        $destination->images()->update(['is_primary' => false]);

        // جعل هذه الصورة رئيسية
        $image->is_primary = true;
        $image->save();
        return redirect()->back()->with([
    'success' => 'Primary image updated successfully.',
    'from' => 'set_primary' ]);
    }

    public function destroyDestination($id)
{
    $destination = Destination::with('images')->findOrFail($id);

    // delete destination from storage
    foreach ($destination->images as $image) {
        Storage::delete('public/' . $image->image_url);
    }


    $destination->delete();

    return redirect()->route('destination.index')->with('success', 'Destination has been deleted successfuly');
}


}
