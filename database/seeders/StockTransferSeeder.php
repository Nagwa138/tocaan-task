<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockTransferSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $items = InventoryItem::has('stocks')->get();

        for ($i = 0; $i < 20; $i++) {
            $source = Warehouse::has('stocks')->inRandomOrder()->first();
            $destination = Warehouse::where('id', '!=', $source->id)->inRandomOrder()->first();
            $item = $items->random();
            $user = $users->random();

            $sourceStock = $item->stocks->where('warehouse_id', $source->id)->first();

            if ($sourceStock) {
                StockTransfer::factory()
                    ->for($source, 'sourceWarehouse')
                    ->for($destination, 'destinationWarehouse')
                    ->for($item)
                    ->for($user)
                    ->create([
                        'quantity' => rand(1, min(10, $sourceStock->quantity))
                    ]);
            }
        }
    }
}
