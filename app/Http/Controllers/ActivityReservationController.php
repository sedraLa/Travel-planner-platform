<?php

namespace App\Http\Controllers;

use App\Models\ActivityReservation;
use Illuminate\Http\Request;
use App\Http\Requests\ActivityReservationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use Carbon\Carbon;

class  ActivityReservationController extends Controller
{
   public function showReservationForm($id)
  {
    $activity = Activity::findOrFail($id);
    return view('activities.reservation-create', compact('activity'));
  }




  public function store(ActivityReservationRequest $request)
{

    $activity = Activity::findOrFail($request->activity_id);

    $activityDate = Carbon::parse($request->activity_date);

    
    if ($activity->start_date && $activityDate->lt($activity->start_date)) {
        return redirect()->back()->withErrors(['Activity not available yet']);
    }

    if ($activity->end_date && $activityDate->gt($activity->end_date)) {
        return redirect()->back()->withErrors(['Activity expired']);
    }

    
    $pricePerPerson = $activity->price;
    $total_price = $request->participants_count * $pricePerPerson;

    // 🧾 إنشاء الحجز
    $reservation = ActivityReservation::create([
        'user_id' => Auth::id(),
        'activity_id' => $request->activity_id,
        'activity_date' => $request->activity_date,
        'participants_count' => $request->participants_count,
        'total_price' => $total_price,
        'status' => 'pending',
        
    ]);
   
    
    return redirect()
        ->route('activity-reservations.pay', $reservation->id)
        ->with('success', 'Reservation created. Please proceed to payment. Total: $' . $total_price);
        
    }



   public function pay($reservationId) {

   
    $reservation = ActivityReservation::findOrFail($reservationId);

   
    return view('activities.pay',compact('reservationId','reservation'));
  }



  public function index(Request $request)

  
{
    $query = ActivityReservation::with(['activity', 'activity.destination', 'user']);

    $isAdmin = Auth::check() && Auth::user()->role === 'admin';

    
    if (!$isAdmin) {
        $query->where('user_id', Auth::id());
    }

    /* =======================
       Keyword search
    ======================= */
    if ($request->filled('keyword')) {
        $term = trim($request->keyword);

        $query->where(function ($q) use ($term, $isAdmin) {

            $q->whereHas('activity', fn ($h) =>
                $h->where('name', 'like', "%{$term}%")
            )
            ->orWhereHas('activity.destination', fn ($d) =>
                $d->where('name', 'like', "%{$term}%")
            );

          
            if ($isAdmin) {
                $q->orWhereHas('user', function ($u) use ($term) {
                    $u->where('name', 'like', "%{$term}%")
                      ->orWhere('last_name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                });
            }
        });
    }

    /* =======================
       Date filters
    ======================= */
    if ($request->filled('activity_date')) {
        $query->whereMonth('activity_date', $request->activity_date);
    }

    

    /* =======================
       Status filter
    ======================= */
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $reservations = $query->latest()->get();

    return view('reservation.index', compact('reservations'));
}




}