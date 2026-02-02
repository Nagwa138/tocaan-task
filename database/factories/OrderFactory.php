<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . now()->format('YmdHis') . rand(100, 999),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 50, 500),
            'items' => [
                [
                    'product' => 'Product ' . rand(1, 5),
                    'quantity' => rand(1, 3),
                    'price' => $this->faker->randomFloat(2, 10, 100),
                ]
            ],
        ];
    }
}
