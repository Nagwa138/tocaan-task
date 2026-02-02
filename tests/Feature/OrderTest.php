<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('TestToken')->plainTextToken;
    }

    public function test_authenticated_user_can_create_order(): void
    {
        $orderData = [
            'items' => [
                [
                    'product' => 'Test Product',
                    'quantity' => 2,
                    'price' => 25.50,
                ],
                [
                    'product' => 'Another Product',
                    'quantity' => 1,
                    'price' => 100.00,
                ],
            ],
            'notes' => 'Test order notes',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                    'id',
                    'order_number',
                    'status',
                    'total_amount',
                    'items',
                    'notes',

            ])
            ->assertJson([
                'status' => 'pending',
                'notes' => 'Test order notes',
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        // Verify total amount calculation
        $total = (2 * 25.5) + (1 * 100);
        $this->assertEqualsWithDelta($total, $response->json()['total_amount'], 0.01);
    }

    public function test_order_creation_requires_items(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/orders', [
            'notes' => 'No items provided',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    public function test_user_can_list_their_orders(): void
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);
        Order::factory()->count(2)->create(); // Other user's orders

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'items' => [
                    '*' => [
                        'id',
                        'order_number',
                        'status',
                        'total_amount',
                        'user',
                    ],
                ],
                'meta',
            ]);

        // Should only see 3 orders (user's own)
        $responseData = $response->json();
        $this->assertCount(3, $responseData['items']);
    }

    public function test_user_can_filter_orders_by_status(): void
    {
        Order::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed',
        ]);

        Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/orders?status=confirmed');

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertCount(2, $responseData['items']);
    }

    public function test_user_can_update_their_pending_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/orders/{$order->id}", Order::factory()->raw([
            'status' => 'confirmed',
            'notes' => 'Updated notes'
        ]));

        $response->assertStatus(200)
            ->assertJson([
                    'status' => 'confirmed',
                    'notes' => 'Updated notes',

            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
            'notes' => 'Updated notes',
        ]);
    }

    public function test_user_cannot_update_order_with_payments(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);

        // Create a payment for the order
        \App\Models\Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/orders/{$order->id}", Order::factory()->raw());

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot update order status after payment has been processed',
            ]);
    }

    public function test_user_can_confirm_pending_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$order->id}/confirm");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id,
                'status' => 'confirmed',
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'items' => $order->items,
                'notes' => $order->notes,
                'created_at' => $order->created_at?->format('F j, Y g:i A'),
                'updated_at' => $order->updated_at?->format('F j, Y g:i A'),
                'user' => [
                    'id' => $order->user_id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ]
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_user_cannot_confirm_non_pending_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$order->id}/confirm");

        $response->assertStatus(422);
    }

    public function test_user_can_cancel_pending_order(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$order->id}/cancel");

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_user_can_delete_order_without_payments(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order deleted successfully!',
            ]);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_user_cannot_delete_order_with_payments(): void
    {
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        \App\Models\Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot delete order after payment has been processed',
            ]);
    }

    public function test_order_requires_authentication(): void
    {
        $response = $this->getJson('/api/orders');
        $response->assertStatus(401);
    }
}
