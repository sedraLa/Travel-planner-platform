<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Transport;
use Illuminate\Support\Facades\DB;
use App\Models\TransportReservation;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Mail\PaymentConfirmationMail;
use App\Mail\TransportPaymentConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NewTransportBookingNotification;
use App\Services\Payments\PaymentContext;
use App\Services\Payments\PaypalPaymentService;
use App\Services\Notifications\PaymentNotificationService;
use App\Services\TransportReservation\ReservationStateManager;
use App\Models\TripReservation;
use App\Models\ActivityReservation;


class PaymentController extends Controller
{
    protected $notificationService;

    public function __construct(PaymentNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /* HOTEL PAYMENT */

    // send user to paypal
    public function payWithPayPal($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);

        if (Auth::id() !== $reservation->user_id) {
            abort(403);
        }

        $context = new PaymentContext(new PaypalPaymentService());

        // create paypal payment
        $response = $context->sendPayment($reservation, 'hotel');

        if ($response['success']) {
            Session::put('paypal_reservation_id', $reservation->id);
            return redirect()->away($response['url']); // go to paypal
        }

        return back()->withErrors('Payment initiation failed.');
    }


    public function paypalCallback(Request $request)
    {
        $context = new PaymentContext(new PaypalPaymentService());


        $result = $context->callBack($request);

        $reservation = Reservation::findOrFail(
            Session::get('paypal_reservation_id')
        );

        if ($result['success']) {

            // update reservation
            $reservation->reservation_status = 'paid';
            $reservation->save();

            // create payment record
            Payment::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'amount' => $reservation->total_price,
                'status' => 'completed',
                'transaction_id' => $result['transaction_id'],
                'payment_date' => now(),
            ]);

            $this->notificationService
                ->sendHotelPaymentConfirmation($reservation);

            return redirect()
                ->route('hotels.index', $reservation->hotel_id)
                ->with('success', 'Payment completed.');
        }

        return back()->withErrors('Payment failed.');
    }

    /* TRANSPORT PAYMENT  */

    public function payWithPayPalTransport()
{
    $reservationId = session('transport_reservation_id');

    if (!$reservationId) {
        abort(400);
    }

    // DTO
    $reservation = TransportReservation::findOrFail($reservationId);

    if ($reservation->user_id !== Auth::id()) {
        abort(403);
    }

    $tempReservation = (object) [
        'id' => $reservation->id,
        'total_price' => $reservation->total_price,
    ];

    $context = new PaymentContext(new PaypalPaymentService());
    $response = $context->sendPayment($tempReservation, 'transport');

    if ($response['success']) {
        return redirect()->away($response['url']);
    }

    return back()->withErrors('Payment initiation failed.');
}



public function paypalCallbackTransport(Request $request)
{
    $context = new PaymentContext(new PaypalPaymentService());
    //check if pay is success
    $result = $context->callBack($request);

    $reservationId = session('transport_reservation_id');
    $reservation = $reservationId ? TransportReservation::find($reservationId) : null;

    if ($result['success'] && $reservation) {
      

        DB::transaction(function () use ($reservation, $result) {
            $stateManager = app(ReservationStateManager::class);
        
        
            $stateManager->transition($reservation, 'pending_payment'); 
            $stateManager->transition($reservation, 'confirmed');       
            $driver = Driver::find($reservation->driver_id);
        
            if ($driver) {
                $driver->update(['last_trip_at' => now()]);
                $driver->increment('total_trips_count');
            }
        
            Payment::create([
                'transport_reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'amount' => $reservation->total_price,
                'status' => 'completed',
                'transaction_id' => $result['transaction_id'],
                'payment_date' => now(),
            ]);
        });

        $this->notificationService->sendTransportPaymentConfirmation($reservation->refresh());


        session()->forget('transport_reservation_id');

       return redirect()->route('vehicle.order')->with('success', 'Transport payment completed.');
    
    }

    return back()->withErrors('Payment failed.');
}

public function payTrip()
{
    $reservationId = session('trip_reservation_id');

    if (!$reservationId) {
        dd(' NO SESSION ID');
    }

    $reservation = TripReservation::findOrFail($reservationId);

    if ($reservation->user_id !== auth()->id()) {
        abort(403);
    }

    $context = new PaymentContext(new PaypalPaymentService());

    $response = $context->sendPayment($reservation, 'trip');

    if ($response['success']) {
        return redirect()->away($response['url']);
    }

    return back()->withErrors('Payment failed');
}

public function paypalCallbackTrip(Request $request)
{
    $context = new PaymentContext(new PaypalPaymentService());
    $result = $context->callBack($request);

    $reservation = TripReservation::find(session('trip_reservation_id'));

    if ($result['success'] && $reservation) {

        $reservation->update(['status' => 'paid']);

        Payment::create([
            'trip_reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'amount' => $reservation->total_price,
            'status' => 'completed',
            'transaction_id' => $result['transaction_id'],
            'payment_date' => now(),
        ]);

        $this->notificationService->sendTripPaymentConfirmation($reservation->refresh());
        

        return redirect()->route('user.trips.index')
            ->with('success', 'Trip booked successfully!');
    }

    return back()->withErrors('Payment failed');
}

public function payWithPayPalActivity($reservationId)
{
    $reservation = ActivityReservation::findOrFail($reservationId);

    if (Auth::id() !== $reservation->user_id) {
        abort(403);
    }

    $context = new PaymentContext(new PaypalPaymentService());
    $response = $context->sendPayment($reservation, 'activity');

    if ($response['success']) {
        Session::put('activity_reservation_id', $reservation->id);
        return redirect()->away($response['url']);
    }

    return back()->withErrors('Payment initiation failed.');
}

public function paypalCallbackActivity(Request $request)
{
    $context = new PaymentContext(new PaypalPaymentService());
    $result = $context->callBack($request);

    $reservation = ActivityReservation::find(
        Session::get('activity_reservation_id')
    );

    if ($result['success'] && $reservation) {
        $reservation->update([
            'status' => 'paid',
        ]);

        Payment::create([
            'activity_reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'amount' => $reservation->total_price,
            'status' => 'completed',
            'transaction_id' => $result['transaction_id'],
            'payment_date' => now(),
        ]);

        Session::forget('activity_reservation_id');

        return redirect()
            ->route('activities.index')
            ->with('success', 'Activity payment completed.');
    }

    return back()->withErrors('Payment failed.');
}

}
