<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;
use App\Models\HotelImage;


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
///deletehotels
public function destroy($id)
{
    $hotel = Hotel::with('images')->findOrFail($id);

    // حذف الصور من التخزين
    foreach ($hotel->images as $image) {
        Storage::delete('public/' . $image->image_url);
    }

    // حذف الفندق من قاعدة البيانات
    $hotel->delete();

    return redirect()->route('hotels.index')->with('success', 'Hotel has been deleted successfully');
}
///deletehotelImage
public function destroyImage($id)
{
    $image = HotelImage::findOrFail($id);

    // تأكد إذا كانت الصورة أساسية
    if ($image->is_primary) {
        return redirect()->back()->withErrors(['error' => 'You cannot delete the primary image.']);
    }

    // حذف الصورة من التخزين
    Storage::delete('public/' . $image->image_url);

    // حذف السطر من قاعدة البيانات
    $image->delete();

    return back()->with('success', 'Image deleted successfully.');
}



}
