<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition()
    {
        $categories = ['Electronics', 'Furniture', 'Office Supplies', 'Tools', 'Materials'];

        return [
            'name' => $this->faker->words(2, true),
            'sku' => strtoupper($this->faker->bothify('??-####')),
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'category' => $this->faker->randomElement($categories),
        ];
    }
}
