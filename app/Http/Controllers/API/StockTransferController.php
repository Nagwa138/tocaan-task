<?php

namespace App\Http\Controllers\API;

use App\Architecture\DTO\StockTransferDTO;
use App\Architecture\Services\Interfaces\IStockTransferService;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\StockTransfer\StockTransferStoreRequest;
use Illuminate\Http\JsonResponse;

class StockTransferController extends Controller
{
    /**
     * @param IStockTransferService $stockTransferService
     */
    public function __construct(
        private readonly IStockTransferService $stockTransferService,
    )
    {}

    /**
     * Store a newly created post with platforms
     */
    public function store(StockTransferStoreRequest $request): JsonResponse
    {
        return $this->stockTransferService->create(array_filter((array)StockTransferDTO::fromRequest($request->safe()->toArray())));
    }
}
