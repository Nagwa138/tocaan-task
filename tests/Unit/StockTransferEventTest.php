<?php

namespace Tests\Unit;

use App\Architecture\Services\Interfaces\IStockTransferService;
use App\Events\LowStockDetected;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class StockTransferEventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        Queue::fake();
    }

    /**
     * @throws Exception
     */
    public function testEventFires()
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

        // Assert event was dispatched
        Event::assertDispatched(LowStockDetected::class, function ($event) use ($stock) {
            return $event->stock->id === $stock->id;
        });
    }
}
