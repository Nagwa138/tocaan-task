<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run()
    {
        $warehouses = Warehouse::all();
        $items = InventoryItem::all();

        foreach ($warehouses as $warehouse) {
            foreach ($items as $item) {
                if (rand(1, 10) <= 7) {
                    Stock::factory()
                        ->for($warehouse)
                        ->for($item)
                        ->create();
                }
            }
        }
    }
}
