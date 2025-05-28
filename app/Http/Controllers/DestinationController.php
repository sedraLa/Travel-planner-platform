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

         $destinations = $query->paginate(8);


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
            'name' => 'required|unique:destinations,name', // make sure that destination name is unique
            'description' => 'nullable',
            'location_details' => 'required',
           // 'weather_info' => 'required',
            'activities' => 'nullable',
            'city' => 'required|string|max:255',
             'country' => 'required|string|max:255',
            'images' => 'required|array',
             'images.*' => 'image|mimes:jpeg,png,jpg,gif',
             'primary_image_index' => 'nullable|integer',
        ]);

        // save destination
        $destination = new Destination();
        $destination->name = $request->name;
        $destination->description = $request->description;
        $destination->location_details = $request->location_details;
        //$destination->weather_info = $request->weather_info;
        $destination->activities = $request->activities;
        $destination->city = $request->city;
        $destination->country = $request->country ;

        $destination->save();

        // if there is any loaded images

        if ($request->hasFile('images')) {
            $images = $request->file('images');


            // save images
            foreach ($images as $index => $image) {
                $imagePath = MediaServices::save($image, 'image', 'Destinations');
                $destination->images()->create([
                    'image_url' => $imagePath,
                    'is_primary' => $request->primary_image_index == $index ? true : false, // if image is primary
                ]);
            }
        }

       
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
    'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
]);


    // update destination info
    $destination->update($validatedData);

    // handle loading new images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $imageFile) {
            $path = $imageFile->store('destinations', 'public');

            DestinationImage::create([
                'destination_id' => $destination->id,
                'image_url' => $path,
                'is_primary' => false,
            ]);
        }
    }




    return redirect()->route('destination.show', $destination->id)->with('success', 'Destination updated successfully');


    }

    /**
     * Remove the specified image from destination.
     */
    public function destroy($id)
{
    $image = DestinationImage::findOrFail($id);

    // check if image is primary
    if ($image->is_primary) {
        return redirect()->back()->withErrors(['error' => 'You cannot delete the primary image.']);;
    }

    // delete image from storage
    Storage::delete('public/' . $image->image_url);

    // delete row from db
    $image->delete();

    return back()->with('success', 'image seleted successfully');
}


    public function setPrimary($id)
    {
        $image = DestinationImage::findOrFail($id);
        $destination = $image->destination;

        // make all images not primary
        $destination->images()->update(['is_primary' => false]);

        // make this image primary
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
