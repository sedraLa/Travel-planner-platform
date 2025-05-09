<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Get all destinations available
        $destinations = Destination::with('images')->get();
        return view('destinations.index',compact('destinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $destination = Destination::with('images')->findOrFail($id);
        return view('destinations.show', compact('destination'));
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
        'weather_info' => 'required|string',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // تحقق من الصور
    ]);

    // تحديث بيانات الـ destination
    $destination->update($validatedData);

    // التعامل مع رفع الصور الجديدة
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('destinations', 'public');  // تخزين الصورة
            $destination->images()->create(['image_url' => $path, 'is_primary' => false]);  // حفظ الصورة في الـ database
        }
    }

    // إعادة التوجيه بعد التحديث
    return redirect()->route('destination.index')->with('success', 'Destination updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}