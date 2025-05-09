<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\DestinationImage;
use App\Services\MediaServices; 


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
    
            // حفظ الصورة الرئيسية إذا تم تحديدها
            if ($request->has('primary_image_index')) {
                $primaryImagePath = MediaServices::save($images[$request->primary_image_index], 'image', 'Destinations');
                $destination->images()->create([
                    'image_url' => $primaryImagePath,
                    'is_primary' => true
                ]);
            }
    
            // حفظ باقي الصور
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