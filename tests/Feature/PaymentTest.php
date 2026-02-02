<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $confirmedOrder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('TestToken')->plainTextToken;

        $this->confirmedOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed',
            'total_amount' => 100.00,
        ]);
    }

    public function test_user_can_list_payments(): void
    {
        Payment::factory()->count(3)->create([
            'order_id' => $this->confirmedOrder->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'items' => [
                    '*' => [
                        'id',
                        'payment_id',
                        'status',
                        'method',
                        'amount',
                        'order',
                    ],
                ],
                'meta',
            ]);
    }

    public function test_user_can_filter_payments_by_order_id(): void
    {
        $otherOrder = Order::factory()->create(['user_id' => $this->user->id]);

        Payment::factory()->create(['order_id' => $this->confirmedOrder->id]);
        Payment::factory()->create(['order_id' => $otherOrder->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/payments?order_id={$this->confirmedOrder->id}");

        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertCount(1, $responseData['items']);
    }

    public function test_user_can_get_available_payment_gateways(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/payments/gateways');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'code',
                        'name',
                        'description',
                        'supports_refund',
                    ],
                ],
            ]);

        // Check if credit_card is available (default enabled gateway)
        $response->assertJsonFragment([
            'code' => 'credit_card',
            'name' => 'Credit Card',
        ]);
    }

    public function test_user_can_process_payment_for_confirmed_order(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus($response->json()['data']['status'] == 'failed' ? 402 : 201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'payment_id',
                    'status',
                    'method',
                    'amount',
                    'order',
                ],
                'message',
                'code',
                'gateway_reference',
            ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $this->confirmedOrder->id,
            'method' => 'credit_card',
            'amount' => 100.00,
        ]);
    }

    public function test_payment_requires_gateway_specific_data(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                // Missing required fields
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'gateway_data.card_number',
            ]);
    }

    public function test_cannot_process_payment_for_pending_order(): void
    {
        $pendingOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$pendingOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_process_payment_for_cancelled_order(): void
    {
        $cancelledOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'cancelled',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$cancelledOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_process_payment_with_invalid_gateway(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'invalid_gateway',
            'gateway_data' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['method']);
    }

    public function test_cannot_process_payment_for_order_with_successful_payment(): void
    {
        // Create a successful payment for the order
        Payment::factory()->create([
            'order_id' => $this->confirmedOrder->id,
            'status' => 'successful',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_prevents_duplicate_payment_attempts(): void
    {
        // First payment attempt
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        // Immediately try second payment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(["message" => "The order is not ready to be paid."]);
    }

    public function test_user_cannot_process_payment_for_other_users_order(): void
    {
        $otherUser = User::factory()->create();
        $otherOrder = Order::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'confirmed',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$otherOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus(403);
    }

    public function test_paypal_payment_requires_payer_email(): void
    {
        config(['payment.enabled_gateways' => ['paypal']]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'paypal',
            'gateway_data' => [
                // Missing payer_email
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gateway_data.payer_email']);
    }

    public function test_credit_card_validation_rejects_invalid_card_number(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '1234', // Invalid - too short
                'card_holder' => 'Test User',
                'expiry_month' => 12,
                'expiry_year' => date('Y') + 1,
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gateway_data.card_number']);
    }

    public function test_credit_card_validation_rejects_expired_card(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/orders/{$this->confirmedOrder->id}/payments/process", [
            'method' => 'credit_card',
            'gateway_data' => [
                'card_number' => '4111111111111111',
                'card_holder' => 'Test User',
                'expiry_month' => 1,
                'expiry_year' => date('Y') - 1, // Expired
                'cvv' => '123',
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gateway_data.expiry_year']);
    }
}
