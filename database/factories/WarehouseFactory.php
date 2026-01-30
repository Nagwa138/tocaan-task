<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition()
    {
        $cities = ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia'];

        return [
            'name' => $this->faker->unique()->company . ' Warehouse',
            'location' => $this->faker->randomElement($cities),
            'description' => $this->faker->sentence,
        ];
    }
}
