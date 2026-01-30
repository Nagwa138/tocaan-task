<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class InventoryItemSeeder extends Seeder
{
    private $items = [
        ['name' => 'Laptop', 'sku' => 'LP-1001', 'price' => 999.99, 'category' => 'Electronics'],
        ['name' => 'Smartphone', 'sku' => 'SP-2002', 'price' => 699.99, 'category' => 'Electronics'],
        ['name' => 'Desk Chair', 'sku' => 'DC-3003', 'price' => 199.99, 'category' => 'Furniture'],
        ['name' => 'Notebook', 'sku' => 'NB-4004', 'price' => 4.99, 'category' => 'Office Supplies'],
        ['name' => 'Pen Set', 'sku' => 'PS-5005', 'price' => 12.99, 'category' => 'Office Supplies'],
    ];

    public function run()
    {
        foreach ($this->items as $item) {
            InventoryItem::create($item);
        }

        // Generate additional random items
        InventoryItem::factory()->count(15)->create();
    }
}
