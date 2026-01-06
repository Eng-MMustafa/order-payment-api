<?php

namespace Tests\Unit;

use App\Services\PaymentGateway\CreditCardGateway;
use App\Services\PaymentGateway\PayPalGateway;
use App\Services\PaymentGateway\BankTransferGateway;
use App\Services\PaymentGateway\StripeGateway;
use App\Services\PaymentGateway\PaymentGatewayFactory;
use App\Services\PaymentGateway\PaymentGatewayInterface;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    /** @test */
    public function it_creates_credit_card_gateway()
    {
        $gateway = PaymentGatewayFactory::create('credit_card');
        $this->assertInstanceOf(CreditCardGateway::class, $gateway);
        $this->assertInstanceOf(PaymentGatewayInterface::class, $gateway);
    }

    /** @test */
    public function it_creates_paypal_gateway()
    {
        $gateway = PaymentGatewayFactory::create('paypal');
        $this->assertInstanceOf(PayPalGateway::class, $gateway);
        $this->assertInstanceOf(PaymentGatewayInterface::class, $gateway);
    }

    /** @test */
    public function it_creates_bank_transfer_gateway()
    {
        $gateway = PaymentGatewayFactory::create('bank_transfer');
        $this->assertInstanceOf(BankTransferGateway::class, $gateway);
        $this->assertInstanceOf(PaymentGatewayInterface::class, $gateway);
    }

    /** @test */
    public function it_creates_stripe_gateway()
    {
        $gateway = PaymentGatewayFactory::create('stripe');
        $this->assertInstanceOf(StripeGateway::class, $gateway);
        $this->assertInstanceOf(PaymentGatewayInterface::class, $gateway);
    }

    /** @test */
    public function it_throws_exception_for_invalid_gateway()
    {
        $this->expectException(InvalidArgumentException::class);
        PaymentGatewayFactory::create('invalid_gateway');
    }

    /** @test */
    public function credit_card_gateway_processes_payment()
    {
        $gateway = new CreditCardGateway();
        
        $paymentData = [
            'card_number' => '4242424242424242',
            'card_expiry_month' => '12',
            'card_expiry_year' => '2025',
            'card_cvv' => '123',
            'amount' => 100.00
        ];
        
        $result = $gateway->processPayment($paymentData);
        
        $this->assertArrayHasKey('payment_id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('transaction_details', $result);
    }

    /** @test */
    public function paypal_gateway_processes_payment()
    {
        $gateway = new PayPalGateway();
        
        $paymentData = [
            'paypal_email' => 'test@example.com',
            'amount' => 100.00
        ];
        
        $result = $gateway->processPayment($paymentData);
        
        $this->assertArrayHasKey('payment_id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /** @test */
    public function gateway_can_check_payment_status()
    {
        $gateway = new CreditCardGateway();
        $paymentId = 'test_payment_123';
        
        $result = $gateway->getPaymentStatus($paymentId);
        
        $this->assertArrayHasKey('payment_id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertEquals($paymentId, $result['payment_id']);
    }
}
