<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\PaymentGateways\CreditCardGateway;
use App\PaymentGateways\PayPalGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Available payment gateways
     */
    protected const GATEWAYS = [
        'credit_card' => CreditCardGateway::class,
        'paypal' => PayPalGateway::class,
    ];

    /**
     * Create a payment gateway instance
     *
     * @param string $gateway
     * @param array $config
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $gateway, array $config = []): PaymentGatewayInterface
    {
        if (!isset(self::GATEWAYS[$gateway])) {
            throw new InvalidArgumentException("Payment gateway '{$gateway}' is not supported");
        }

        $gatewayClass = self::GATEWAYS[$gateway];
        return new $gatewayClass($config);
    }

    /**
     * Get all available payment gateways
     *
     * @return array
     */
    public static function getAvailableGateways(): array
    {
        return array_keys(self::GATEWAYS);
    }
}
