<?php

namespace App\PaymentGateways;

use App\Interfaces\PaymentGatewayInterface;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getName(): string
    {
        return static::class;
    }
}
