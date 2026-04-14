<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests\GuideRequest;
use App\Models\User;
use App\Models\Guide;
use App\Services\MediaServices;
use Illuminate\Support\Facades\Storage;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverStatusMail;
use Carbon\Carbon;
use App\Models\TripReservation;


class GuideController extends Controller
{

    public function assignedTrips()
    {
        $user = auth()->user();
    
        $guide = $user->guide;
    
        $assignments = $guide->assignments()
            ->where('status', 'assigned')
            ->with([
                'trip.primaryDestination',
                'trip.schedules',
                'trip.days.activities.activity'
            ])
            ->latest()
            ->get();

       $reservations = TripReservation::where('guide_id', $guide->id)->get()->keyBy('trip_id');

        return view('guide.assigned-trips', compact('assignments','reservations'));
     
    }


    public function completeTrip($reservationId)
    {
        $guide = auth()->user()->guide;

        // نجيب الحجز مع schedule
        $reservation = TripReservation::with('schedule')
            ->where('id', $reservationId)
            ->where('guide_id', $guide->id)
            ->firstOrFail();

        // تأكد أن الرحلة انتهت
        if (now()->lt($reservation->schedule->end_date)) {
            return back()->with('error', 'Trip is not finished yet.');
        }

        // منع التكرار
        if ($reservation->guide_paid_at) {
            return back()->with('error', 'This trip already completed.');
        }

        // حساب الربح (20%)
        $earning = round($reservation->total_price * 0.20, 2);

        // تحديث الحجز
        $reservation->update([
            'guide_earning' => $earning,
            'guide_paid_at' => now(),
        ]);

        // إضافة رصيد للمرشد
        $guide->increment('earnings_balance', $earning);

        return back()->with('success', 'Trip completed successfully and earning added.');
    }

}
