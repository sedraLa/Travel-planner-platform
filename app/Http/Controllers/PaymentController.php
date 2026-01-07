<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Transport;
use App\Models\TransportReservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Mail\PaymentConfirmationMail;
use App\Mail\TransportPaymentConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NewTransportBookingNotification;
use App\Services\Payments\PaymentContext;
use App\Services\Payments\PaypalPaymentService;
use App\Services\Notifications\PaymentNotificationService;


class PaymentController extends Controller
{

    protected $notificationService;

    public function __construct(PaymentNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function payWithPayPal($reservationId)
{
    $reservation = Reservation::findOrFail($reservationId);

    if (Auth::id() !== $reservation->user_id) {
        abort(403);
    }

    $context = new PaymentContext(new PaypalPaymentService());
    $response = $context->sendPayment($reservation, 'hotel');    

    if ($response['success']) {
        Session::put('paypal_reservation_id', $reservation->id);
        return redirect()->away($response['url']);
    }

    return back()->withErrors('Payment initiation failed.');
}



    public function paypalCallback(Request $request)
{
    $context = new PaymentContext(new PaypalPaymentService());
    $result = $context->callBack($request);
        

    $reservation = Reservation::findOrFail(Session::get('paypal_reservation_id'));

    if ($result['success']) {

        $reservation->reservation_status = 'paid';
        $reservation->save();

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

        return redirect()->route('hotels.index', $reservation->hotel_id)
            ->with('success', 'Payment completed.');
    }

    return back()->withErrors('Payment failed.');
}




public function payWithPayPalTransport($reservationId)
{
    $reservation = TransportReservation::findOrFail($reservationId);

    if (Auth::id() !== $reservation->user_id) {
        abort(403);
    }

    $context = new PaymentContext(new PaypalPaymentService());
    $response = $context->sendPayment($tempReservation, 'transport');



    if ($response['success']) {
        Session::put('paypal_transport_reservation_id', $reservation->id);
        return redirect()->away($response['url']);
    }

    return back()->withErrors('Payment initiation failed.');
}


public function paypalCallbackTransport(Request $request)
{
    $context = new PaymentContext(new PaypalPaymentService());
    $result = $context->callBack($request);
    

    $data = session('transport_reservation_data');

    if ($result['success'] && $data) {
        
        $reservation = TransportReservation::create(array_merge($data, [
            'user_id' => Auth::id(),
            'status' => 'completed',
            'transport_vehicle_id' => $data['vehicle_id'],
        ]));



        $this->notificationService
        ->sendTransportPaymentConfirmation($reservation);
    

        Payment::create([
            'transport_reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'amount' => $reservation->total_price,
            'status' => 'completed',
            'transaction_id' => $result['transaction_id'],
            'payment_date' => now(),
        ]);

     


        session()->forget('transport_reservation_data');

        return redirect()->route('transport.index')
    ->with('success', 'Transport payment completed and reservation created.');

    }

    return back()->withErrors('Payment failed.');
}

}


