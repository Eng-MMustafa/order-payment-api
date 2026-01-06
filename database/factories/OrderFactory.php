<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'confirmed', 'cancelled'];
        
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'shipping_address' => $this->faker->address(),
            'billing_address' => $this->faker->address(),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->paragraph(),
        ];
    }
    
    /**
     * Indicate that the order is in pending status.
     */
    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
    
    /**
     * Indicate that the order is in confirmed status.
     */
    public function confirmed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }
    
    /**
     * Indicate that the order is in cancelled status.
     */
    public function cancelled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
