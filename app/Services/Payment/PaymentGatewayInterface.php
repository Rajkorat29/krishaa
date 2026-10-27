<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Initialize/create a payment transaction for an order.
     */
    public function createPayment(Order $order): array;

    /**
     * Verify payment signature or callback.
     */
    public function verifyPayment(array $payload): bool;

    /**
     * Get the identifier name of this payment gateway.
     */
    public function getName(): string;
}
