<?php

namespace App\Services\Payment;

use Razorpay\Api\Api;
use Exception;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder(int $amount, string $receiptId, string $currency = 'USD')
    {
        try {
            $orderData = [
                'receipt' => $receiptId,
                'amount' => $amount, // Amount in lowest denomination (e.g. cents)
                'currency' => $currency,
                'payment_capture' => 1 // Auto capture
            ];

            $razorpayOrder = $this->api->order->create($orderData);

            return $razorpayOrder;
        } catch (Exception $e) {
            Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyPaymentSignature(array $attributes)
    {
        try {
            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (Exception $e) {
            Log::error('Razorpay Signature Verification Failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getPaymentDetails($paymentId)
    {
        try {
            return $this->api->payment->fetch($paymentId);
        } catch (Exception $e) {
            Log::error('Razorpay Fetch Payment Failed: ' . $e->getMessage());
            return null;
        }
    }
}
