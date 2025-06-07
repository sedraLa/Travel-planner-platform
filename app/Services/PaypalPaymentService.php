<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaypalPaymentService
{
    protected $base_url;
    protected $header;

    public function __construct()
    {
        $this->base_url = config('services.paypal.base_url'); // من config/services.php
        $this->header = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
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


        return $response->json()['access_token'];
    }

    public function sendPayment($reservation)
    {
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
                'return_url' => route('payment.paypal.callback'),
                'cancel_url' => route('payment.paypal.callback'),
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
            $links = $response->json()['links'];
            $approveLink = collect($links)->firstWhere('rel', 'approve')['href'];

            return [
                'success' => true,
                'url' => $approveLink,
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error initiating PayPal payment',
            ];
        }
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
        ->send('POST', $url, [
            'body' => '{}',
        ]);

    \Log::info('PayPal Capture Response:', $response->json());

    if (!$response->successful()) {
        return ['success' => false, 'message' => 'HTTP request failed with status ' . $response->status()];
    }

    $responseData = $response->json();

    if (
        isset($responseData['status']) &&
        $responseData['status'] === 'COMPLETED'
    ) {
        return ['success' => true, 'message' => 'Payment completed successfully!'];
    }

    return ['success' => false, 'message' => 'Payment failed! Status: ' . ($responseData['status'] ?? 'unknown')];
}

    

    
    

}
