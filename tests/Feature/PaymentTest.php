<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        
        // Generate token
        $this->token = JWTAuth::fromUser($this->user);
        
        // Create a confirmed order for payment testing
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed',
            'total_amount' => 200.00
        ]);
    }

    /** @test */
    public function user_can_process_payment_for_confirmed_order()
    {
        $paymentData = [
            'order_id' => $this->order->id,
            'payment_method' => 'credit_card',
            'card_number' => '4242424242424242',
            'card_expiry_month' => '12',
            'card_expiry_year' => '2025',
            'card_cvv' => '123'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/payments/process', $paymentData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'order_id',
                    'payment_id',
                    'status',
                    'payment_method',
                    'amount',
                    'transaction_details',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $this->order->id,
            'payment_method' => 'credit_card',
            'amount' => 200.00
        ]);
    }

    /** @test */
    public function payment_cannot_be_processed_for_non_confirmed_order()
    {
        // Change order status to pending
        $this->order->update(['status' => 'pending']);

        $paymentData = [
            'order_id' => $this->order->id,
            'payment_method' => 'credit_card',
            'card_number' => '4242424242424242',
            'card_expiry_month' => '12',
            'card_expiry_year' => '2025',
            'card_cvv' => '123'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/payments/process', $paymentData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'This order cannot be processed for payment. Order status must be confirmed.'
            ]);
    }

    /** @test */
    public function user_can_view_payment_details()
    {
        // Create a payment
        $payment = Payment::factory()->create([
            'order_id' => $this->order->id,
            'payment_method' => 'credit_card',
            'amount' => 200.00,
            'status' => 'successful'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'order_id',
                    'payment_id',
                    'status',
                    'payment_method',
                    'amount',
                    'transaction_details',
                    'created_at',
                    'updated_at',
                    'order'
                ],
                'gateway_status'
            ]);
    }

    /** @test */
    public function user_can_view_all_payments()
    {
        // Create some payments
        Payment::factory()->count(3)->create([
            'order_id' => $this->order->id,
            'status' => 'successful'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'order_id',
                            'payment_id',
                            'status',
                            'payment_method',
                            'amount'
                        ]
                    ],
                    'current_page',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]
            ]);
    }
}
