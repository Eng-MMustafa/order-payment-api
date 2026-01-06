<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user
        $this->user = User::factory()->create();
        
        // Generate token
        $this->token = JWTAuth::fromUser($this->user);
    }

    /** @test */
    public function user_can_create_an_order()
    {
        $orderData = [
            'shipping_address' => '123 Test Street, Test City',
            'billing_address' => '123 Test Street, Test City',
            'items' => [
                [
                    'product_name' => 'Test Product',
                    'quantity' => 2,
                    'unit_price' => 100.00
                ]
            ],
            'notes' => 'Test order notes'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'order_number',
                    'shipping_address',
                    'billing_address',
                    'total_amount',
                    'status',
                    'notes',
                    'created_at',
                    'updated_at',
                    'order_items'
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'shipping_address' => $orderData['shipping_address'],
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => 'Test Product',
            'quantity' => 2,
            'unit_price' => 100.00,
            'total_price' => 200.00
        ]);
    }

    /** @test */
    public function user_can_view_their_orders()
    {
        // Create some orders for the user
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'user_id',
                            'order_number',
                            'shipping_address',
                            'billing_address',
                            'total_amount',
                            'status'
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

    /** @test */
    public function user_can_update_their_pending_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $updateData = [
            'shipping_address' => 'Updated Address',
            'status' => 'confirmed'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/orders/{$order->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => [
                    'shipping_address' => 'Updated Address',
                    'status' => 'confirmed'
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'shipping_address' => 'Updated Address',
            'status' => 'confirmed'
        ]);
    }

    /** @test */
    public function user_can_delete_order_without_payments()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id
        ]);
    }
}
