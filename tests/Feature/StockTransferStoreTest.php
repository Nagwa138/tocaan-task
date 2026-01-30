<?php

namespace Tests\Feature;

use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class StockTransferStoreTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    CONST STOCK_TRANSFER_URL = '/api/stock-transfers';

    public function testAuthenticationRequired()
    {
        $response = $this->json('post',self::STOCK_TRANSFER_URL, []);
        $response->assertStatus(401);
    }

    public function testQuantityEmpty()
    {
        $this->validateInput('quantity', ['quantity' => '']);
    }

    public function testQuantityString()
    {
        $this->validateInput('quantity', ['quantity' => "Hola"]);
    }

    public function testSourceEmpty()
    {
        $this->validateInput('source_warehouse_id', ['source_warehouse_id' => '']);
    }

    public function testSourceString()
    {
        $this->validateInput('source_warehouse_id', ['source_warehouse_id' => "Hola"]);
    }

    public function testSourceNotExists()
    {
        $this->validateInput('source_warehouse_id', ['source_warehouse_id' => 2]);
    }


    public function testDestinationEmpty()
    {
        $this->validateInput('destination_warehouse_id', ['destination_warehouse_id' => '']);
    }

    public function testDestinationString()
    {
        $this->validateInput('destination_warehouse_id', ['destination_warehouse_id' => "Hola"]);
    }

    public function testDestinationNotExists()
    {
        $this->validateInput('destination_warehouse_id', ['destination_warehouse_id' => 2]);
    }

    public function testInventoryEmpty()
    {
        $this->validateInput('inventory_item_id', ['inventory_item_id' => '']);
    }

    public function testInventoryString()
    {
        $this->validateInput('inventory_item_id', ['inventory_item_id' => "Hola"]);
    }

    public function testInventoryNotExists()
    {
        $this->validateInput('inventory_item_id', ['inventory_item_id' => 2]);
    }

    public function testStockTransferSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $stock = Stock::factory()->create([
            'quantity' => 10,
            'low_stock_threshold' => 5,
        ]);
        $stockTransferData =  StockTransfer::factory()->raw([
            'user_id' => $user->id, 'source_warehouse_id' => $stock->warehouse_id,
            'inventory_item_id' => $stock->inventory_item_id, 'quantity' => $stock->quantity - $stock->quantity + 1 + $stock->low_stock_threshold,
        ]);
        $response = $this->json('post',self::STOCK_TRANSFER_URL,$stockTransferData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
           'message', 'stock_transfer' => [
                "id" ,  "source_warehouse" ,  "destination_warehouse" ,"inventory_item", "user", "quantity","status" ,
                "notes",   "created_at"
            ]
        ]);

        $this->assertDatabaseHas("stock_transfers", Arr::except($stockTransferData, ['status', 'notes']));
    }

    private function validateInput($input, $data)
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->json('post',self::STOCK_TRANSFER_URL, $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors($input);
    }
}
