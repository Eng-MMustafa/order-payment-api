<?php

namespace App\Services\PaymentGateway;

interface PaymentGatewayInterface
{
    /**
     * Process a payment.
     *
     * @param array $paymentData
     * @return array
     */
    public function processPayment(array $paymentData): array;

    /**
     * Get payment status.
     *
     * @param string $paymentId
     * @return array
     */
    public function getPaymentStatus(string $paymentId): array;
}
