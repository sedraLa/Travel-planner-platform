<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Services\PaypalPaymentService; 
use Illuminate\Support\Facades\Session;
use App\Mail\PaymentConfirmationMail;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function payWithPayPal($reservationId)
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
    }

    public function paypalCallback(Request $request)
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
    }
}

