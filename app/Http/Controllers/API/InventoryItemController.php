<?php

namespace App\Http\Controllers\API;

use App\Architecture\Services\Interfaces\IInventoryItemService;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Inventory\InventoryItemListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class InventoryItemController extends Controller
{
    /**
     * @param IInventoryItemService $inventoryItemService
     */
    public function __construct(
        private readonly IInventoryItemService $inventoryItemService
    )
    {}

    /**
     * @param InventoryItemListRequest $request
     * @return JsonResponse
     */
    public function index(InventoryItemListRequest $request): JsonResponse
    {
        if ($warehouseId = Arr::get($request->getFilters(), 'id')) {
            $cacheKey = "warehouses_{$warehouseId}_inventory";
        } else {
            $cacheKey = "warehouses_inventory";
        }

        // Cache for 30 minutes (1800 seconds)
        $minutes = 30;

        return Cache::remember($cacheKey, $minutes * 60, function () use ($request) {
            return $this->inventoryItemService->list($request->getFilters(), $request->getPerPage());
        });
    }
}
