<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaypalPaymentService
{
    protected $base_url;   //api url
    protected $header; //info we send with each http request/response

    public function __construct()
    {
        $this->base_url = config('services.paypal.base_url');
        $this->header = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];
    }

        //get access token from paypal
    protected function getAccessToken()
    {
        $response = Http::asForm()->withBasicAuth(
            config('services.paypal.client_id'),
            config('services.paypal.client_secret')
        )->post($this->base_url . '/v1/oauth2/token', [
            'grant_type' => 'client_credentials',
        ]);


        return $response->json()['access_token'];
    }

    //send request to paypal to create payment order
    public function sendPayment($reservation, $type = 'hotel')
    {
        $callback = $type === 'transport'
            ? route('payment.transport.callback')
            : route('payment.paypal.callback');

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($reservation->total_price, 2, '.', ''),
                ],
                'description' => 'Payment for reservation #' . $reservation->id,
            ]],
            'application_context' => [
                'return_url' => $callback,
                'cancel_url' => $callback,
                'brand_name' => config('app.name'),
                'landing_page' => 'LOGIN',
                'user_action' => 'PAY_NOW',
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ];

        $response = Http::withHeaders($this->header)
            ->asJson()
            ->post($this->base_url . '/v2/checkout/orders', $data);

        if ($response->successful()) {
            $approveLink = collect($response->json()['links'])
                ->firstWhere('rel', 'approve')['href'];

            return ['success' => true, 'url' => $approveLink];
        }

        return ['success' => false, 'message' => 'Error initiating PayPal payment'];
    }

    public function callBack($request)
    {
        $token = $request->get('token');

        if (!$token) {
            return ['success' => false, 'message' => 'Missing PayPal token'];
        }

        $url = $this->base_url . '/v2/checkout/orders/' . $token . '/capture';

        $response = Http::withToken($this->getAccessToken())
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->send('POST', $url, ['body' => '{}']);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'HTTP failed with status ' . $response->status()];
        }

        $data = $response->json();

        if (isset($data['status']) && $data['status'] === 'COMPLETED') {
            return [
                'success' => true,
                'transaction_id' => $data['id'] ?? null,
            ];
        }

        return ['success' => false, 'message' => 'Payment failed'];
    }






}
