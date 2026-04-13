<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Services\GeocodingService;
use App\Models\Destination ;


class UserTripController extends Controller
{

public function index(Request $request)
{
    $query = Trip::with('primaryDestination');

  
   if ($request->filled('search')) {
    $query->where('name', 'like', '%' . $request->search . '%');
       } 

  
    if ($request->filled('destination_id')) {
        $query->where('destination_id', $request->destination_id);
    }

   
    if ($request->filled('max_participants')) {
        $query->where('max_participants', '>=', $request->max_participants);
    }

   
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

   

      $trips = $query->latest()->paginate(6);

    $categories = Trip::whereNotNull('category')->distinct()->pluck('category');
   $destinations = Destination::whereIn('id',Trip::whereNotNull('destination_id')->pluck('destination_id')->unique())->get();

    return view('trips.user.index', compact('trips', 'destinations','categories'));
    }




  public function show(string $id, GeocodingService $geo)
{
    $trip = Trip::with([
        'days.activities.activity', 'days.hotel', 'packages.highlights',
        'packages.includes', 'packages.excludes', 'packages.packageHotels.hotel',
        'packages.infos', 'schedules', 'assignedGuide', 'assignments.guide',
        'transports', 'primaryDestination', 'images',
    ])->findOrFail($id);

    // 1. جلب العنوان الذي أدخلته أنت
    $addressToSearch = $trip->meeting_point_address;

    $coords = null;
    if ($addressToSearch) {
        // محاولة جلب الإحداثيات للعنوان المدخل (مثل Trocadéro Gardens, Paris)
        $coords = $geo->geocodeAddress($addressToSearch);
    }

    // 2. إذا فشل البحث بالعنوان الدقيق، نجرب دمج العنوان مع المدينة والدولة
    if (!$coords && $trip->primaryDestination) {
        $fallbackAddress = $addressToSearch . ', ' . 
                          $trip->primaryDestination->city . ', ' . 
                          $trip->primaryDestination->country;
        $coords = $geo->geocodeAddress($fallbackAddress);
    }

    // 3. إذا ظل فارغاً، نضع إحداثيات باريس يدوياً كحل أخير إذا كان العنوان يحتوي على Paris
    if (!$coords && str_contains(strtolower($addressToSearch), 'paris')) {
        $coords = ['latitude' => 48.8584, 'longitude' => 2.2945]; // إحداثيات برج إيفل/تروكاديرو
    }

    // 4. القيم الافتراضية النهائية (إذا فشل كل شيء)
   $coords = $coords ?? null;

    return view('trips.user.show', compact('trip', 'coords'));
}

}
