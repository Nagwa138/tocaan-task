<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            WarehouseSeeder::class,
            InventoryItemSeeder::class,
            StockSeeder::class,
            UserSeeder::class,
            StockTransferSeeder::class,
        ]);
    }
}
