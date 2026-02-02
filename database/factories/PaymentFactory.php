<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payment_id' => 'txn_' . now()->timestamp . rand(1000, 9999),
            'order_id' => Order::factory(),
            'status' => $this->faker->randomElement(['pending', 'successful', 'failed']),
            'method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer']),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'gateway_response' => [
                'success' => true,
                'message' => 'Payment processed',
            ],
        ];
    }
}
