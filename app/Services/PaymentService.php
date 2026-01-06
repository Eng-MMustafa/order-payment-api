<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentGateway\PaymentGatewayFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process payment for an order.
     *
     * @param Order $order
     * @param array $paymentData
     * @return Payment
     * @throws Exception
     */
    public function processPayment(Order $order, array $paymentData): Payment
    {
        // Check if order can be processed for payment
        if (!$order->canProcessPayment()) {
            throw new Exception('Payment can only be processed for confirmed orders.');
        }

        // Start a database transaction
        return DB::transaction(function () use ($order, $paymentData) {
            try {
                // Get the appropriate payment gateway
                $gateway = PaymentGatewayFactory::create($paymentData['payment_method']);
                
                // Process the payment through the gateway
                $result = $gateway->processPayment($paymentData);
                
                // Create a payment record
                $payment = new Payment([
                    'order_id' => $order->id,
                    'payment_id' => $result['payment_id'],
                    'status' => $result['status'],
                    'payment_method' => $paymentData['payment_method'],
                    'amount' => $order->total_amount,
                    'transaction_details' => $result['transaction_details'] ?? null
                ]);
                
                $payment->save();
                
                return $payment;
            } catch (Exception $e) {
                Log::error('Payment processing failed: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Get payment status from the gateway.
     *
     * @param Payment $payment
     * @return array
     */
    public function getPaymentStatus(Payment $payment): array
    {
        try {
            $gateway = PaymentGatewayFactory::create($payment->payment_method);
            return $gateway->getPaymentStatus($payment->payment_id);
        } catch (Exception $e) {
            Log::error('Failed to get payment status: ' . $e->getMessage());
            return [
                'payment_id' => $payment->payment_id,
                'status' => $payment->status,
                'message' => 'Unable to get live status from gateway',
                'timestamp' => now()->toIso8601String()
            ];
        }
    }
}
