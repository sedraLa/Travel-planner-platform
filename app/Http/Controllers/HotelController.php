<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Destination;
use App\Models\HotelImage;
use App\Models\Reservation;
use App\Http\Requests\HotelRequest;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;
use App\Services\GeocodingService;


class HotelController extends Controller
{

    ///index
public function index(Request $request)
{
    $query = Hotel::with('images');

    // filter
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('city', 'like', '%' . $searchTerm . '%')
              ->orWhere('country', 'like', '%' . $searchTerm . '%');
        });
    }

    // 
    if ($request->filled('rating')) {
        $query->where('global_rating', $request->rating);
    }

    
    if ($request->filled('price_per_night')) {
        $query->where('price_per_night', '<=', $request->price_per_night);
    }

    $hotels = $query->paginate(8)->appends($request->query());

    return view('hotel.index', compact('hotels'));
}



////show

public function show(string $id, GeocodingService $geo)
{
    $hotel = Hotel::with('images')->findOrFail($id);
    $primaryImage = $hotel->images->where('is_primary', true)->first();
    $fullAddress = implode(', ', array_filter([
        $hotel->address,
        $hotel->city,
        $hotel->country
    ]));
    $coords = $geo->geocodeAddress($fullAddress);
    return view('hotel.show', compact('hotel', 'primaryImage', 'coords'));
}

///create

public function create()
 {
    //get destinations for select
    $destinations = Destination::all();
    return view('hotel.create',compact('destinations'));
 }

 ///store

 public function store(HotelRequest $request) {
    //save hotel details
    $hotel = Hotel::create([
        'name' => $request->name,
        'description' => $request->description,
        'address' => $request->address,
        'price_per_night' => $request->price_per_night,
        'global_rating' => $request->global_rating,
        'total_rooms' => $request->total_rooms,
        'destination_id' => $request->destination_id,
        'city' => $request->city,
        'country' => $request->country,
    ]);

    //save images

    if($request->hasFile('images')) {
        foreach($request->file('images') as $index => $image) {
            $imagePath = MediaServices::save($image,'image','Hotels');

            $hotel->images()->create([
                'image_url' => $imagePath,
                'is_primary' => $request->primary_image_index==$index,
            ]);
        }
    }

    return redirect()->route('hotels.index')->with('success','Hotel has been created successfully');
 }

 ///edit

 public function edit($id)
{
    $hotel = Hotel::with('images')->findOrFail($id);
    $destinations = Destination::all();
    return view('hotel.edit', compact('hotel', 'destinations'));
}

///update

public function update(HotelRequest $request, $id) {
    $hotel = Hotel::findOrFail($id);
    $hotel->update($request->validated());

//handle new image upload
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $imageFile) {
            $path = MediaServices::save($imageFile, 'image', 'hotels');

            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_url' => $path,
                'is_primary' => false,
            ]);
        }
    }

    return redirect()->route('hotel.show', $hotel->id)->with('success', 'Hotel updated successfully');

}

//delete image

public function destroyImage($id)
{
    $image = HotelImage::findOrFail($id);

    if ($image->is_primary) {
        return redirect()->back()->withErrors(['error' => 'You cannot delete the primary image.']);
    }

    Storage::delete('public/' . $image->image_url);
    $image->delete();

    return back()->with('success', 'Image deleted successfully.');
}

//set primary

public function setPrimaryImage($id) {
    $image = HotelImage::findOrFail($id);
    $hotel = $image->hotel;

    $hotel->images()->update(['is_primary' => false]);

    $image->is_primary = true;
    $image->save();

    return back()->with('success', 'Primary image set successfully.');

}

///delete
public function destroy($id)
{
    $hotel = Hotel::with('images')->findOrFail($id);
    $reservations = $hotel->reservations;
    if ($hotel->reservations->count()) {
        return redirect()->back()->withErrors(['error' => 'You cannot delete this hotel, it has reservations.']);
    }


    // delete images from storage
    foreach ($hotel->images as $image) {
        Storage::delete('public/' . $image->image_url);
    }

    // delete hotel from db
    $hotel->delete();

    return redirect()->route('hotels.index')->with('success', 'Hotel has been deleted successfully');
}
}



