<?php

namespace App\Architecture\Services\Classes;

use App\Architecture\Repositories\Interfaces\IStockRepository;
use App\Architecture\Repositories\Interfaces\IStockTransferRepository;
use App\Architecture\Repositories\Interfaces\IWarehouseRepository;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Interfaces\IStockTransferService;
use App\Events\LowStockDetected;
use App\Http\Resources\StockTransferResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StockTransferService implements IStockTransferService
{
    /**
     * @param IStockTransferRepository $stockTransferRepository
     * @param IWarehouseRepository $warehouseRepository
     * @param IStockRepository $stockRepository
     * @param IApiHttpResponder $apiHttpResponder
     */
    public function __construct(
        private readonly IStockTransferRepository $stockTransferRepository,
        private readonly IWarehouseRepository $warehouseRepository,
        private readonly IStockRepository $stockRepository,
        private readonly IApiHttpResponder        $apiHttpResponder
    )
    {}

    /**
     * List Platforms
     *
     * @param array $data
     * @return JsonResponse
     */
    public function create(array $data): JsonResponse
    {
        try {
            $data['status'] = 'pending';

            $sourceWarehouse = $this->warehouseRepository->first(['id' => $data['source_warehouse_id']]);
            $destinationWarehouse = $this->warehouseRepository->first(['id' => $data['destination_warehouse_id']]);

            if (
                $sourceStock = $this->stockRepository->first([
                    'warehouse_id' => $data['source_warehouse_id'],
                    'inventory_item_id' => $data['inventory_item_id'],
                ])
            ) {
                if ($sourceStock->quantity >= $data['quantity']) {

                    Cache::forget("warehouse_{$data['source_warehouse_id']}_inventory");
                    Cache::forget("warehouse_{$data['destination_warehouse_id']}_inventory");
                    Cache::forget("warehouse_inventory");

                    $data['status'] = 'completed';
                    $data['notes'] = 'Warehouse ID ' . $sourceWarehouse->id . ' Transfer ' . $data['quantity'] . ' from Item ' . $data['inventory_item_id'] . ' to Warehouse ID ' . $destinationWarehouse->id;

                    $sourceStock->quantity -= $data['quantity'];
                    $sourceStock->save();

                    if ($sourceStock->quantity <= $sourceStock->low_stock_threshold) LowStockDetected::dispatch($sourceStock);

                    if (
                        $destinationStock = $this->stockRepository->first([
                            'warehouse_id' => $data['destination_warehouse_id'],
                            'inventory_item_id' => $data['inventory_item_id'],
                        ])
                    ) {
                        $destinationStock->quantity += $data['quantity'];
                        $destinationStock->save();
                    } else {
                        $this->stockRepository->create([
                            'quantity' => $data['quantity'],
                            'warehouse_id' => $destinationWarehouse->id,
                            'inventory_item_id' => $data['inventory_item_id'],
                        ]);
                    }
                } else {
                    $data['status'] = 'cancelled';
                    $data['notes'] = 'Quantity available is less than needed';
                }
            } else {
                $data['status'] = 'cancelled';
                $data['notes'] = 'Source warehouse does not has any of this inventory item';
            }

            $transfer = $this->stockTransferRepository->create($data);

            return $this->apiHttpResponder->sendSuccess([
                "message" => $transfer->status == 'completed' ? "Stock transfer completed" : "Stock transfer cancelled",
                "stock_transfer" => new StockTransferResource($transfer),
            ], 201);

        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage());
        }
    }
}
