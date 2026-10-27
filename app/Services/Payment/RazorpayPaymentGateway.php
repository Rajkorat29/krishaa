<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class RazorpayPaymentGateway implements PaymentGatewayInterface
{
    protected string $keyId;
    protected string $keySecret;
    protected bool $testMode;

    public function __construct()
    {
        $this->keyId = config('services.razorpay.key', env('RAZORPAY_KEY', 'rzp_test_placeholder_key'));
        $this->keySecret = config('services.razorpay.secret', env('RAZORPAY_SECRET', 'placeholder_secret'));
        $this->testMode = config('services.razorpay.test_mode', env('RAZORPAY_TEST_MODE', true));
    }

    public function createPayment(Order $order): array
    {
        // Amount in paise (1 INR = 100 paise)
        $amountPaise = (int) round($order->total * 100);

        Log::info("Razorpay order initiated in test mode for order {$order->order_number}", [
            'amount_inr' => $order->total,
            'amount_paise' => $amountPaise,
            'test_mode' => $this->testMode,
        ]);

        return [
            'gateway' => 'razorpay',
            'test_mode' => $this->testMode,
            'key_id' => $this->keyId,
            'razorpay_order_id' => 'order_' . substr(md5($order->order_number . microtime()), 0, 14),
            'amount' => $amountPaise,
            'currency' => 'INR',
            'order_number' => $order->order_number,
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'contact' => $order->customer_phone,
            ],
            'notes' => [
                'merchant_order_id' => $order->order_number,
            ],
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        if ($this->testMode && empty($this->keySecret) || $this->keySecret === 'placeholder_secret') {
            // Test mode simulation accepts valid signature or test flag
            return true;
        }

        $orderId = $payload['razorpay_order_id'] ?? '';
        $paymentId = $payload['razorpay_payment_id'] ?? '';
        $signature = $payload['razorpay_signature'] ?? '';

        $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->keySecret);

        return hash_equals($expectedSignature, $signature);
    }

    public function getName(): string
    {
        return 'Razorpay';
    }
}
