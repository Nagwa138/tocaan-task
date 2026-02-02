<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@inventory.test',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Warehouse Manager',
            'email' => 'manager@inventory.test',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Inventory Clerk',
            'email' => 'clerk@inventory.test',
            'password' => bcrypt('password'),
        ]);


        // Create 3 users
        $users = User::factory(3)->create();

        foreach ($users as $user) {
            // Create 2 orders for each user
            $orders = Order::factory(2)->create([
                'user_id' => $user->id,
            ]);

            foreach ($orders as $order) {
                // Create 1 payment for each order
                Payment::factory()->create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                ]);
            }
        }

    }
}
