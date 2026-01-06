<?php

namespace App\Services\PaymentGateway;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Create a payment gateway instance.
     *
     * @param string $gateway
     * @return PaymentGatewayInterface
     * @throws \InvalidArgumentException
     */
    public static function create(string $gateway): PaymentGatewayInterface
    {
        $config = config('payment_gateways.' . $gateway);
        if (!$config || !Arr::has($config, 'class')) {
            throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}");
        }

        $class = $config['class'];
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Payment gateway class not found: {$class}");
        }

        $instance = new $class();
        if (!$instance instanceof PaymentGatewayInterface) {
            throw new InvalidArgumentException("Payment gateway does not implement required interface: {$class}");
        }

        return $instance;
    }
}
