<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;

interface PaymentStrategy
{
    public function sendPayment($reservation, string $type);
    public function callBack(Request $request);
}
