<?php
namespace App\Services\Payments;

use Illuminate\Http\Request;

class PaymentContext
{
    protected PaymentStrategy $strategy;

    public function __construct(PaymentStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(PaymentStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function sendPayment($reservation, string $type)
    {
        return $this->strategy->sendPayment($reservation, $type);
    }

    public function callBack(Request $request)
    {
        return $this->strategy->callBack($request);
    }
}
