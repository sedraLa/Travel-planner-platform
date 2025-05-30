<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Destination;
use App\Models\HotelImage;
use App\Http\Requests\HotelRequest;
use App\Services\MediaServices;



class HotelController extends Controller
{

    ///index
public function index(Request $request)
{
    $query = Hotel::with('images');

    // تحقق إن كانت هناك كلمة بحث مدخلة
    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;

        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('city', 'like', '%' . $searchTerm . '%')
              ->orWhere('country', 'like', '%' . $searchTerm . '%')
              ->orWhere('global_rating', 'like', '%' . $searchTerm . '%'); // مضاف: البحث بالفئة
        });
    }

    $hotels = $query->paginate(8);

    return view('hotel.index', compact('hotels'));
}


////show

public function show(string $id)
{
    $hotel = Hotel::with('images')->findOrFail($id);
    $primaryImage = $hotel->images->where('is_primary', true)->first();
    return view('hotel.show', compact('hotel', 'primaryImage'));
}

///create

public function create()
 {
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

}
