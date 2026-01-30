<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'source_warehouse_id' => Warehouse::factory(),
            'destination_warehouse_id' => Warehouse::factory(),
            'inventory_item_id' => InventoryItem::factory(),
            'user_id' => User::factory(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'status' => 'completed',
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * State for pending transfers
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * State for cancelled transfers
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'notes' => $this->faker->sentence(), // Cancelled transfers usually have a note
            ];
        });
    }

    /**
     * State with specific source warehouse
     */
    public function fromWarehouse($warehouse)
    {
        return $this->state(function (array $attributes) use ($warehouse) {
            return [
                'source_warehouse_id' => is_int($warehouse) ? $warehouse : $warehouse->id,
            ];
        });
    }

    /**
     * State with specific destination warehouse
     */
    public function toWarehouse($warehouse)
    {
        return $this->state(function (array $attributes) use ($warehouse) {
            return [
                'destination_warehouse_id' => is_int($warehouse) ? $warehouse : $warehouse->id,
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

    /**
     * State with specific user
     */
    public function byUser($user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => is_int($user) ? $user : $user->id,
            ];
        });
    }

    /**
     * State with small quantity transfer
     */
    public function smallQuantity()
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity' => $this->faker->numberBetween(1, 10),
            ];
        });
    }

    /**
     * State with large quantity transfer
     */
    public function largeQuantity()
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity' => $this->faker->numberBetween(100, 1000),
            ];
        });
    }
}
