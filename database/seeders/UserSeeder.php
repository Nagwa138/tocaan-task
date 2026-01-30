<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        // Additional test users
        User::factory()->count(5)->create();
    }
}
