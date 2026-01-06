<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'successful', 'failed'];
        $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'stripe'];
        
        return [
            'order_id' => Order::factory(),
            'payment_id' => 'payment_' . Str::uuid(),
            'status' => $this->faker->randomElement($statuses),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'transaction_details' => json_encode([
                'processor' => $this->faker->randomElement($paymentMethods) . ' Gateway',
                'timestamp' => now()->toIso8601String(),
                'reference' => 'REF-' . $this->faker->randomNumber(6)
            ]),
        ];
    }
    
    /**
     * Indicate that the payment is successful.
     */
    public function successful(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'successful',
        ]);
    }
    
    /**
     * Indicate that the payment is pending.
     */
    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
    
    /**
     * Indicate that the payment has failed.
     */
    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
