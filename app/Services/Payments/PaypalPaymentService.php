<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PaypalPaymentService implements PaymentStrategy
{
    protected $base_url;
    protected $header;

    public function __construct()
    {
        $this->base_url = config('services.paypal.base_url');

        $token = $this->getAccessToken();

        if (!$token) {
            dd(' PayPal Token Failed');
        }

        $this->header = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
    }

    protected function getAccessToken()
    {
        $response = Http::asForm()->withBasicAuth(
            config('services.paypal.client_id'),
            config('services.paypal.client_secret')
        )->post($this->base_url . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);

        if (!$response->successful()) {
            dd(' TOKEN ERROR', $response->status(), $response->json());
        }

        //get access token from response
        return $response->json()['access_token'] ?? null;
    }

    public function sendPayment($reservation, $type = 'hotel')
    {
     
        $callback = match ($type) {
            'transport' => route('payment.transport.callback'),
            'trip' => route('payment.trip.callback'),
            'activity' => route('payment.activity.callback'),
            default => route('payment.paypal.callback'),
        };

       //make sure a price existed
        if (!$reservation->total_price || $reservation->total_price <= 0) {
            dd(' INVALID PRICE', $reservation);
        }

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($reservation->total_price, 2, '.', ''),
                ],
                'description' => 'Payment for reservation #' . $reservation->id,
            ]],
            //User UI on paypal
            'application_context' => [
                'return_url' => $callback,
                'cancel_url' => $callback,
                'brand_name' => config('app.name'),
                'landing_page' => 'LOGIN',
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];

        //send payment request to paypal 
        $response = Http::withHeaders($this->header)
            ->asJson()
            ->post($this->base_url . '/v2/checkout/orders', $data);

 
        if (!$response->successful()) {
            dd(' PAYPAL ERROR', $response->status(), $response->json());
        }

        //extract pay approval link
        $approveLink = collect($response->json()['links'])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (!$approveLink) {
            dd(' NO APPROVE LINK', $response->json());
        }

        return ['success' => true, 'url' => $approveLink];
    }

    public function callBack(Request $request)
    {
        //PayPal Order ID
        $token = $request->get('token');

        if (!$token) {
            return ['success' => false, 'message' => 'Missing PayPal token'];
        }

        //the actual payment
        $url = $this->base_url . '/v2/checkout/orders/' . $token . '/capture';
        $response = Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->send('POST', $url, ['body' => '{}']);

        if (!$response->successful()) {
            dd(' CAPTURE ERROR', $response->status(), $response->json());
        }

        $data = $response->json();

        if (($data['status'] ?? null) === 'COMPLETED') {
            return [
                'success' => true,
                'transaction_id' => $data['id'] ?? null,
            ];
        }

        return ['success' => false, 'message' => 'Payment failed'];
    }
}