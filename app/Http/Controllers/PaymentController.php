<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Transport;
use App\Models\TransportReservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\PaypalPaymentService;
use App\Mail\PaymentConfirmationMail;
use App\Mail\TransportPaymentConfirmationMail;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NewTransportBookingNotification;


class PaymentController extends Controller
{
    /*public function payWithPayPal($reservationId)
    {
        //check if there is a reservation
        $reservation = Reservation::findOrFail($reservationId);

        if (Auth::id() !== $reservation->user_id) {
            abort(403, 'Unauthorized action.');
        }

        //create paypal service

        $paypalService = new PaypalPaymentService();

        //start payment process
        $response = $paypalService->sendPayment($reservation);

        //if request has been created successfully
        if ($response['success']) {
            // store reservation id in session so we can know it easilly
            Session::put('paypal_reservation_id', $reservation->id);

            // take the user to payment url which been given to us from paypal
            return redirect()->away($response['url']);
        } else {
            return redirect()->route('reservations.pay', $reservation->id)->withErrors(['Payment initiation failed. Please try again.']);
        }
    }*/

    public function payWithPayPal($reservationId)
{
    $reservation = Reservation::findOrFail($reservationId);

    if (Auth::id() !== $reservation->user_id) {
        abort(403);
    }

    $paypal = new PaypalPaymentService();
    $response = $paypal->sendPayment($reservation, 'hotel');

    if ($response['success']) {
        Session::put('paypal_reservation_id', $reservation->id);
        return redirect()->away($response['url']);
    }

    return back()->withErrors('Payment initiation failed.');
}


   /* public function paypalCallback(Request $request)
    {
        $paypalService = new PaypalPaymentService();

        //continue payment process
        $response = $paypalService->callBack($request);

        //get reservation id from session
        $reservationId = Session::get('paypal_reservation_id');
        $reservation = Reservation::findOrFail($reservationId);

        //update reservation status according to payment result

        if ($response['success']) {

            $reservation->reservation_status = 'paid';
            $reservation->save();

            //store payment process
            Payment::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'amount' => $reservation->total_price,
                'status' => 'completed',
                'transaction_id' => $response['transaction_id'] ?? null,
                'payment_date' => now(),
            ]);

            //sending email
            Mail::to($reservation->user->email)->send(new PaymentConfirmationMail($reservation->hotel->name, $reservation));

            return redirect()->route('hotel.show', $reservation->hotel_id)->with('success', "Payment completed successfully! Your reservation is now completed. we'v sent you an email ");
        } else {
            return redirect()->route('reservations.pay', $reservation->id)->withErrors(['Payment failed. Please try again.']);
        }
    }*/

    public function paypalCallback(Request $request)
{
    $paypal = new PaypalPaymentService();
    $result = $paypal->callBack($request);

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

        Mail::to($reservation->user->email)
            ->send(new PaymentConfirmationMail($reservation->hotel->name, $reservation));

        return redirect()->route('hotel.show', $reservation->hotel_id)
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

    $paypal = new PaypalPaymentService();
    $response = $paypal->sendPayment($reservation, 'transport');

    if ($response['success']) {
        Session::put('paypal_transport_reservation_id', $reservation->id);
        return redirect()->away($response['url']);
    }

    return back()->withErrors('Payment initiation failed.');
}


public function paypalCallbackTransport(Request $request)
{
    $paypal = new PaypalPaymentService();
    $result = $paypal->callBack($request);

    $data = session('transport_reservation_data');

    if ($result['success'] && $data) {
        // إنشاء الحجز بعد الدفع مباشرة
        $reservation = TransportReservation::create(array_merge($data, [
            'user_id' => Auth::id(),
            'status' => 'completed',
            'transport_vehicle_id' => $data['vehicle_id'],
        ]));



        // driver notification
$reservation->driver->user->notify(
    new NewTransportBookingNotification($reservation)
);

// traveler notification
$reservation->user->notify(
    new NewTransportBookingNotification($reservation)
);

        Payment::create([
            'transport_reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'amount' => $reservation->total_price,
            'status' => 'completed',
            'transaction_id' => $result['transaction_id'],
            'payment_date' => now(),
        ]);

        // إرسال ايميل تأكيد
        Mail::to(Auth::user()->email)
            ->send(new TransportPaymentConfirmationMail($reservation));

        session()->forget('transport_reservation_data');

        return redirect()->route('transport.index')
    ->with('success', 'Transport payment completed and reservation created.');

    }

    return back()->withErrors('Payment failed.');
}

}


