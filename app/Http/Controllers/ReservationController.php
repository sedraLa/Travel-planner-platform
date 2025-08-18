<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Requests\ReservationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Hotel;
use Carbon\Carbon;

class ReservationController extends Controller
{
   public function showReservationForm($id)
{
    $hotel = Hotel::findOrFail($id);
    return view('reservation.create', compact('hotel'));
}
public function store(ReservationRequest $request)
{

    $hotel = Hotel::findOrFail($request->hotel_id);

    $checkIn = Carbon::parse($request->check_in_date);
    $checkOut = Carbon::parse($request->check_out_date);

    // check reserved rooms in required period
    $overlappingReservations = Reservation::where('hotel_id', $hotel->id)
        ->where(function ($query) use ($checkIn, $checkOut) {
            $query->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);   //old reservation overlapped with new one
        })
        ->sum('rooms_count');   //reserved rooms count

    // available rooms in this period
    $availableRooms = $hotel->total_rooms - $overlappingReservations;

    // check if there is enough rooms
    if ($availableRooms < $request->rooms_count) {
        return redirect()->back()->withErrors(['Not enough rooms available for the selected dates!']);
    }

    // calculate nights between in and out dates
    $days = max(1, $checkIn->diffInDays($checkOut)); // 1 means make sure that nights number at least = one night
    //calculate total price
    $room_price = $hotel->price_per_night;
    $total_price = $days * $request->rooms_count * $room_price;

    /** @var \App\Models\User|null $user */
$user = Auth::user();
if ($user) {
    // عبّي القيم لو وصلت من الفورم
    foreach (['last_name','phone_number','country'] as $k) {
        if ($request->filled($k)) {
            $user->{$k} = $request->input($k);
        }
    }

    // احفظ بس إذا صار تغيير فعلي (وبيسكت الـ IDE)
    if ($user->isDirty(['last_name','phone_number','country'])) {
        $user->save();
        // اختياري: تحقّق سريع
        // dump($user->only(['last_name','phone_number','country']));
    }
}



    // create the reservation
    $reservation = Reservation::create([
        'user_id' => Auth::check() ? Auth::id() : null,
        'hotel_id' => $request->hotel_id,
        'check_in_date' => $request->check_in_date,
        'check_out_date' => $request->check_out_date,
        'rooms_count' => $request->rooms_count,
        'guest_count' => $request->guest_count,
        'total_price' => $total_price,
        'reservation_status' => 'pending',
    ]);



    return redirect()->route('reservations.pay', $reservation->id)->with('success', 'Thanks, Your reservation now is pending please click on pay. Total cost: $' . $total_price);
}

//pay

public function pay($reservationId) {
    $reservation = Reservation::findOrFail($reservationId);
    if (Auth::id() !== $reservation->user_id) {
        abort(403,'Unauthorized action');
    }
    return view('reservation.pay',compact('reservationId','reservation'));
}
public function index(Request $request)
{
    $query = Reservation::with(['hotel','user','hotel.destination']);

    // غير الأدمن يشوف حجوزاته فقط
    if (!Auth::check() || Auth::user()->role !== 'admin') {
        $query->where('user_id', Auth::id());
    }

    // بحث للأدمن فقط
    if (Auth::check() && Auth::user()->role === 'admin' && $request->filled('search')) {
        $term = trim($request->search);

        // === التعرف على "شهر" للـ check-in (3 صيغ مدعومة) ===
        // 1) YYYY-MM  مثل 2025-08  => سنة + شهر
        // 2) MM فقط   مثل 08 / 8   => شهر فقط
        // 3) اسم شهر  مثل August   => شهر فقط
        $month = null; $year = null;

        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $term)) {
            [$year, $m] = explode('-', $term);
            $month = (int) $m;
            $year  = (int) $year;
        } elseif (preg_match('/^(0?[1-9]|1[0-2])$/', $term)) {
            $month = (int) $term;
        } else {
            // اسم شهر (English) مثل August / Sep ...
            try {
                if (preg_match('/[A-Za-z]+/', $term)) {
                    $month = Carbon::parse('1 '.$term.' 2000')->month; // يعطي رقم الشهر
                }
            } catch (\Throwable $e) {
                // تجاهل إذا ما قدر يparse
            }
        }

        $query->where(function ($q) use ($term, $month, $year) {
            // فندق بالاسم
            $q->whereHas('hotel', function ($h) use ($term) {
                $h->where('name', 'like', "%{$term}%");
            })

            // مدينة/دولة/اسم الوجهة عبر destination
            ->orWhereHas('hotel.destination', function ($d) use ($term) {
                $d->where('city', 'like', "%{$term}%")
                  ->orWhere('country', 'like', "%{$term}%")
                  ->orWhere('name', 'like', "%{$term}%"); // 👈 اسم الـ destination
            })

            // مستخدم: الاسم المركّب (first + last) أو الإيميل أو name إن وُجد
            ->orWhereHas('user', function ($u) use ($term) {
                $u->whereRaw("CONCAT(COALESCE(name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$term}%"])
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('name',  'like', "%{$term}%"); // لو عندك عمود name
            });

            // 🔎 بحث شهر check-in حصراً (OR مع الشروط السابقة)
            if ($month) {
                $q->orWhere(function($qq) use ($month, $year) {
                    $qq->whereMonth('check_in_date', $month);
                    if ($year) {
                        $qq->whereYear('check_in_date', $year);
                    }
                });
            }
        });
    }

    // نفس أسلوبك: بدون paginate
    $reservations = $query->get();

    return view('reservation.index', compact('reservations'));
}


}
