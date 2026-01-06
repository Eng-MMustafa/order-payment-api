<?php

namespace App\Services\PaymentGateway;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    /**
     * Gateway configuration.
     *
     * @var array
     */
    protected $config;
    
    /**
     * Gateway name.
     *
     * @var string
     */
    protected $gatewayName;
    
    /**
     * Constructor.
     *
     * @param string $gatewayName
     */
    public function __construct(string $gatewayName = null)
    {
        $this->gatewayName = $gatewayName ?? $this->getGatewayName();
        $this->loadConfig();
    }
    
    /**
     * Get the gateway name from class name if not specified.
     *
     * @return string
     */
    protected function getGatewayName(): string
    {
        $className = class_basename(static::class);
        return strtolower(str_replace('Gateway', '', $className));
    }
    
    /**
     * Load gateway configuration from config file.
     *
     * @return void
     */
    protected function loadConfig(): void
    {
        $this->config = config('payment_gateways.' . $this->gatewayName, []);
    }

    /**
     * Generate a unique payment ID.
     *
     * @return string
     */
    protected function generatePaymentId(): string
    {
        return uniqid('payment_', true);
    }

    /**
     * Log payment transaction.
     *
     * @param array $data
     * @return void
     */
    protected function logTransaction(array $data): void
    {
        // In a real application, we would log transaction details here
        // For now, we'll just log to Laravel's log
        \Log::info('Payment Transaction', $data);
    }
    
    /**
     * Check if gateway is in sandbox mode.
     *
     * @return bool
     */
    protected function isSandbox(): bool
    {
        return $this->config['sandbox'] ?? true;
    }
}
