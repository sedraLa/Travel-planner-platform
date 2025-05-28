<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;


class HotelController extends Controller
{

    ///index
public function index(Request $request)
{
    $query = Hotel::with('image');

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
    return view('hotels.show', compact('hotel', 'primaryImage'));
}




}
