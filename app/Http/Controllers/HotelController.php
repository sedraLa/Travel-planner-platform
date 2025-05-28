<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;


class HotelController extends Controller
{
    public function index(Request $request)
{
    $query = Hotel::with('image'); // جلب الفندق مع الصورة الأساسية (عبر علاقة image)

    // البحث بالاسم أو الموقع (المدينة والدولة إذا كانت موجودة)
    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;

        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('city', 'like', '%' . $searchTerm . '%')
              ->orWhere('country', 'like', '%' . $searchTerm . '%');
        });
    }

    $hotels = $query->get();

    return view('hotel.index', compact('hotels'));
}

}
