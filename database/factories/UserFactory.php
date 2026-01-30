<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        if (!app()->bound('hash')) {
            app()->bootstrapWith(['Illuminate\Foundation\Bootstrap\LoadConfiguration']);
        }

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function warehouseManager()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Manager ' . $this->faker->lastName,
                'email' => 'manager' . $this->faker->unique()->numberBetween(1, 100) . '@example.com',
            ];
        });
    }
}
