<?php

use App\Architecture\Services\Interfaces\IStockTransferService;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StockTransferUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testStockTransferSuccess()
    {
        $stockTransferService = app(IStockTransferService::class);

        $stock = Stock::factory()->create([
            'quantity' => 15,
            'low_stock_threshold' => 10
        ]);

        $stockTransfer = StockTransfer::factory()->raw([
            'quantity' => 6,
            'source_warehouse_id' => $stock->warehouse_id,
            'inventory_item_id' => $stock->inventory_item_id,
        ]);

        $stockTransferService->create($stockTransfer);

        $stockTransfer['status'] = 'completed';
        $stockTransfer['notes'] = 'Warehouse ID ' . $stock->warehouse_id . ' Transfer ' . $stockTransfer['quantity'] . ' from Item ' . $stockTransfer['inventory_item_id'] . ' to Warehouse ID ' . $stockTransfer['destination_warehouse_id'];

        $this->assertDatabaseHas("stock_transfers", $stockTransfer);

        $this->assertDatabaseHas("stocks", [
            'warehouse_id' => $stock->warehouse_id,
            'inventory_item_id' => $stock->inventory_item_id,
            'quantity' => $stock->quantity - $stockTransfer['quantity'],
        ]);

        $this->assertDatabaseHas("stocks", [
            'warehouse_id' => $stockTransfer['destination_warehouse_id'],
            'inventory_item_id' => $stock->inventory_item_id,
            'quantity' => $stockTransfer['quantity'],
        ]);

    }

    public function testStockTransferCancelledForNoStock()
    {
        $stockTransferService = app(IStockTransferService::class);

        $stock = Stock::factory()->create([
            'quantity' => 15,
            'low_stock_threshold' => 10
        ]);

        $stockTransfer = StockTransfer::factory()->raw([
            'quantity' => 16,
            'source_warehouse_id' => $stock->warehouse_id,
            'inventory_item_id' => $stock->inventory_item_id,
        ]);

        $stockTransferService->create($stockTransfer);

        $stockTransfer['status'] = 'cancelled';
        $stockTransfer['notes'] = 'Quantity available is less than needed';

        $this->assertDatabaseHas("stock_transfers", $stockTransfer);

        $this->assertDatabaseHas("stocks", [
            'id' => $stock->id,
            'warehouse_id' => $stock->warehouse_id,
            'inventory_item_id' => $stock->inventory_item_id,
            'quantity' => $stock->quantity,
        ]);

        $this->assertDatabaseMissing("stocks", [
            'warehouse_id' => $stockTransfer['destination_warehouse_id'],
            'inventory_item_id' => $stock->inventory_item_id,
            'quantity' => $stockTransfer['quantity'],
        ]);
    }



    public function testStockTransferCancelledForNoQuantityEnough()
    {
        $stockTransferService = app(IStockTransferService::class);

        $warehouse = Warehouse::factory()->create();

        $stockTransfer = StockTransfer::factory()->raw([
            'quantity' => 6,
            'source_warehouse_id' => $warehouse->id,
        ]);

        $stockTransferService->create($stockTransfer);

        $stockTransfer['status'] = 'cancelled';
        $stockTransfer['notes'] = 'Source warehouse does not has any of this inventory item';

        $this->assertDatabaseHas("stock_transfers", $stockTransfer);
    }
}
