<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'inventory_item_id' => InventoryItem::factory(),
            'quantity' => $this->faker->numberBetween(0, 100),
            'low_stock_threshold' => $this->faker->numberBetween(5, 20),
        ];
    }

    /**
     * State for low stock items
     */
    public function lowStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity' => $this->faker->numberBetween(1, 5),
                'low_stock_threshold' => 10,
            ];
        });
    }

    /**
     * State for out of stock items
     */
    public function outOfStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity' => 0,
            ];
        });
    }

    /**
     * State with specific warehouse
     */
    public function forWarehouse($warehouse)
    {
        return $this->state(function (array $attributes) use ($warehouse) {
            return [
                'warehouse_id' => is_int($warehouse) ? $warehouse : $warehouse->id,
            ];
        });
    }

    /**
     * State with specific inventory item
     */
    public function forItem($item)
    {
        return $this->state(function (array $attributes) use ($item) {
            return [
                'inventory_item_id' => is_int($item) ? $item : $item->id,
            ];
        });
    }
}
