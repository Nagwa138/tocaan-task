<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        Warehouse::factory()->create([
            'name' => 'Main Warehouse',
            'location' => 'New York',
            'description' => 'Primary distribution center'
        ]);

        Warehouse::factory()->create([
            'name' => 'West Coast Hub',
            'location' => 'Los Angeles',
            'description' => 'West coast operations'
        ]);

        Warehouse::factory()->create([
            'name' => 'Southern Depot',
            'location' => 'Houston',
            'description' => 'Southern regional warehouse'
        ]);

        // Additional warehouses
        Warehouse::factory()->count(5)->create();
    }
}
