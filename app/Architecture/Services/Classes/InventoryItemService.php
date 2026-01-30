<?php

namespace App\Architecture\Services\Classes;

use App\Architecture\Repositories\Interfaces\IInventoryItemRepository;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Interfaces\IInventoryItemService;
use Illuminate\Http\JsonResponse;

class InventoryItemService implements IInventoryItemService
{
    /**
     * @param IInventoryItemRepository $inventoryItemRepository
     * @param IApiHttpResponder $apiHttpResponder
     */
    public function __construct(
        private readonly IInventoryItemRepository $inventoryItemRepository,
        private readonly IApiHttpResponder        $apiHttpResponder
    )
    {}

    /**
     * List Platforms
     *
     * @param array $filters
     * @param int $perPage
     * @return JsonResponse
     */
    public function list(array $filters = [], int $perPage = 10): JsonResponse
    {
        return $this->apiHttpResponder->sendSuccess(
            $this->inventoryItemRepository->list($filters, $perPage)->toArray()
            );
    }
}
