<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DestinationRequest;
use App\Http\Requests\HighlightRequest;
use App\Models\Destination;
use App\Models\Highlight;
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

         $destinations = $query->paginate(9);


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
    public function store(DestinationRequest $request)
    {
        // save destination details
        $destination = Destination::create([
            'name' => $request->name,
            'description' => $request->description,
            'location_details' => $request->location_details,
            //'activities' => $request->location_details,
            'city' => $request->city,
            'country' => $request->country,
            'iata_code' => strtoupper($request['iata_code']),
            'timezone'=>$request->timezone,
            'language'=> $request->language,
            'currency'=> $request->currency,
            'nearest_airport'=> $request->nearest_airport,
            'best_time_to_visit'=> $request->best_time_to_visit,
            'emergency_numbers'=> $request->emergency_numbers,
            'local_tip'=> $request->local_tip,

        ]);



         if ($request->filled('highlights')) {
        foreach ($request->highlights as $title) {
            if (!empty($title)) {
                $destination->highlights()->create([
                    'title' => $title,
                ]);
            }
        }
    }

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
        $destination = Destination::with(['images','highlights'])->findOrFail($id);
        $primaryImage = $destination->images->where('is_primary', true)->first();
        return view('destinations.show', compact('destination', 'primaryImage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    $destination = Destination::with('highlights', 'images')->findOrFail($id);

    return view('destinations.edit', compact('destination'));
    }  

    /**
     * Update the specified resource in storage.
     */
    public function update(DestinationRequest $request, string $id)
    {

    $destination = Destination::findOrFail($id);
    // update destination info
    $destination->update($request->validated());


    if ($request->filled('highlight')) {
    foreach ($request->highlight as $highlightText) {
        if (!empty($highlightText)) {
            $destination->highlights()->create([
                'title' => $highlightText
            ]);
        }
    }
}



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


    return redirect()->route('destination.index', $destination->id)->with('success', 'Destination updated successfully');


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

    return back()->with('success', 'image deleted successfully');
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
