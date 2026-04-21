<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Destination;
use App\Models\HotelImage;
use App\Models\RoomType;
use App\Models\Reservation;
use App\Http\Requests\HotelRequest;
use App\Http\Requests\HotelRoomTypesRequest;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\DB;


class HotelController extends Controller
{

    ///index
    public function index(Request $request)
    {
        
        $query = Hotel::with(['images', 'destination','reviews.user']);
    
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%")
                  ->orWhere('global_rating', 'like', "%{$search}%");
            });
        }
    
        // Filters
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        if ($request->filled('stars')) {
            $query->where('stars', (int) $request->stars);
        }
        if ($request->filled('pets_allowed')) {
            $query->where('pets_allowed', $request->pets_allowed);
        }
        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->filled('amenities')) {
            $query->where(function($q) use ($request) {
                foreach ($request->amenities as $amenity) {
                    $q->orWhereJsonContains('amenities', $amenity);
                }
            });
        }

         if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        $selectedDestination = null;
        if ($request->filled('destination_id')) {
            $selectedDestination = Destination::find($request->destination_id);
        }
    

        $hotels = $query->paginate(9);
    
        return view('hotel.index', compact('hotels', 'selectedDestination'));
    }
    


////show

public function show(string $id, GeocodingService $geo)
{
    $hotel = Hotel::with([
        'images',
        'reviews.user',
        'roomTypes.images',
        'roomTypes.primaryImage',
    ])->findOrFail($id);

    
    $primaryImage = $hotel->images->where('is_primary', true)->first();
    $fullAddress = implode(', ', array_filter([
        $hotel->address,
        $hotel->city,
        $hotel->country
    ]));
    
    $coords = $geo->geocodeAddress($fullAddress);
    
  
    if (!$coords) {
        $coords = $geo->geocodeAddress($hotel->city . ', ' . $hotel->country);
    }
    
  
    $coords = $coords ?? ['latitude' => null, 'longitude' => null];
    
    $isBooked = Reservation::where('hotel_id', $hotel->id)
         ->where('user_id', auth()->id())
         ->exists();
    
    return view('hotel.show', compact('hotel', 'primaryImage', 'coords','isBooked'));
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
    $hotel = null;

    DB::transaction(function () use ($request, &$hotel) {
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
            'stars'            => $request->stars,
            'pets_allowed' => $request->has('pets_allowed'),
            'check_in_time'   => $request->check_in_time,
            'check_out_time'   => $request->check_out_time,
            'policies'         => $request->policies,
            'phone_number'     => $request->phone_number,
            'email'            => $request->email,
            'website'          => $request->website,
            'nearby_landmarks' => $request->nearby_landmarks,
            'amenities'        => $request->amenities,
        ]);

        //save hotel images (existing logic)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = MediaServices::save($image, 'image', 'Hotels');

                $hotel->images()->create([
                    'image_url' => $imagePath,
                    'is_primary' => $request->primary_image_index == $index,
                ]);
            }
        }

        $this->syncRoomTypes($hotel, $request, false);
    });

    return redirect()->route('hotels.index')->with('success','Hotel has been created successfully');
 }

 ///edit

public function edit($id)
{
    $hotel = Hotel::with(['images', 'roomTypes.images'])->findOrFail($id);
    $destinations = Destination::all();
    return view('hotel.edit', compact('hotel', 'destinations'));
}

public function editRoomTypes($id)
{
    $hotel = Hotel::with(['roomTypes.images'])->findOrFail($id);

    return view('Hotel.room-types-edit', compact('hotel'));
}

public function updateRoomTypes(HotelRoomTypesRequest $request, $id)
{
    $hotel = Hotel::with('roomTypes.images')->findOrFail($id);

    DB::transaction(function () use ($request, $hotel) {
        $this->syncRoomTypes($hotel, $request, true);
    });

    return redirect()->route('hotels.edit', $hotel->id)
        ->with('success', 'Room types updated successfully.');
}

///update

public function update(HotelRequest $request, $id) {
    $hotel = Hotel::with('roomTypes.images')->findOrFail($id);

    DB::transaction(function () use ($request, $hotel) {
        $hotel->update($request->validated());

        //handle new image upload (existing logic)
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

        $this->syncRoomTypes($hotel, $request, true);
    });

    return redirect()->route('hotels.index', $hotel->id)->with('success', 'Hotel updated successfully');

}

private function syncRoomTypes(Hotel $hotel, Request $request, bool $isUpdate): void
{
    $roomTypesData = $request->input('room_types', []);
    $roomTypeFiles = $request->file('room_types', []);
    $submittedIds = collect($roomTypesData)
        ->pluck('id')
        ->filter()
        ->map(fn ($id) => (int) $id)
        ->all();

    if ($isUpdate) {
        $deletedRoomTypes = $hotel->roomTypes()->whereNotIn('id', $submittedIds)->with('images')->get();
        foreach ($deletedRoomTypes as $deletedRoomType) {
            foreach ($deletedRoomType->images as $image) {
                Storage::delete('public/' . $image->image_url);
            }
            $deletedRoomType->delete();
        }
    }

    foreach ($roomTypesData as $index => $roomData) {
        $amenities = array_values(array_filter(array_map('trim', explode(',', (string) ($roomData['amenities'] ?? '')))));

        $roomType = isset($roomData['id']) && $roomData['id']
            ? $hotel->roomTypes()->where('id', $roomData['id'])->first()
            : null;

        if (!$roomType) {
            $roomType = $hotel->roomTypes()->create([
                'name' => $roomData['name'],
                'price_per_night' => $roomData['price_per_night'],
                'capacity' => $roomData['capacity'],
                'quantity' => $roomData['quantity'],
                'description' => $roomData['description'] ?? null,
                'amenities' => $amenities,
                'is_refundable' => isset($roomData['is_refundable']),
            ]);
        } else {
            $roomType->update([
                'name' => $roomData['name'],
                'price_per_night' => $roomData['price_per_night'],
                'capacity' => $roomData['capacity'],
                'quantity' => $roomData['quantity'],
                'description' => $roomData['description'] ?? null,
                'amenities' => $amenities,
                'is_refundable' => isset($roomData['is_refundable']),
            ]);
        }

        $removeImageIds = collect($roomData['remove_existing_image_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->all();

        if (!empty($removeImageIds)) {
            $imagesToDelete = $roomType->images()->whereIn('id', $removeImageIds)->get();
            foreach ($imagesToDelete as $imageToDelete) {
                Storage::delete('public/' . $imageToDelete->image_url);
                $imageToDelete->delete();
            }
        }

        $newImageIds = [];
        $uploadedImages = data_get($roomTypeFiles, "{$index}.images", []);
        foreach ($uploadedImages as $imageFile) {
            $imagePath = MediaServices::save($imageFile, 'image', 'room-types');
            $newImage = $roomType->images()->create([
                'image_url' => $imagePath,
                'is_primary' => false,
            ]);
            $newImageIds[] = $newImage->id;
        }

        $primaryChoice = $roomData['primary_image_choice'] ?? $roomData['primary_new_image_choice'] ?? null;
        if (!$primaryChoice && array_key_exists('primary_image_index', $roomData) && $roomData['primary_image_index'] !== '') {
            $primaryChoice = 'new:' . (int) $roomData['primary_image_index'];
        }
        $roomType->images()->update(['is_primary' => false]);

        if (is_string($primaryChoice) && str_starts_with($primaryChoice, 'existing:')) {
            $primaryId = (int) str_replace('existing:', '', $primaryChoice);
            $roomType->images()->where('id', $primaryId)->update(['is_primary' => true]);
        } elseif (is_string($primaryChoice) && str_starts_with($primaryChoice, 'new:')) {
            $newIndex = (int) str_replace('new:', '', $primaryChoice);
            if (isset($newImageIds[$newIndex])) {
                $roomType->images()->where('id', $newImageIds[$newIndex])->update(['is_primary' => true]);
            }
        }

        if (!$roomType->images()->where('is_primary', true)->exists()) {
            $firstImage = $roomType->images()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }
    }
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
