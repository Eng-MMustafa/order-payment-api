<?php

namespace App\Interfaces;

interface PaymentGatewayInterface
{
    public function processPayment(array $paymentData): array;
    public function getPaymentStatus(string $paymentId): array;
    public function refundPayment(string $paymentId): array;
    public function getName(): string;
}
