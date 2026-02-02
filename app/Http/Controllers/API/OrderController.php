<?php

namespace App\Http\Controllers\API;

use App\Architecture\Services\Interfaces\IOrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\OrderDeleteRequest;
use App\Http\Requests\API\Order\OrderListRequest;
use App\Http\Requests\API\Order\OrderStoreRequest;
use App\Http\Requests\API\Order\OrderUpdateRequest;
use App\Http\Requests\API\Order\OrderUpdateStatusRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * @param IOrderService $orderService
     */
    public function __construct(
        private readonly IOrderService $orderService,
    )
    {}

    /**
     * Store a newly created post with platforms
     */
    public function index(OrderListRequest $request): JsonResponse
    {
        return $this->orderService->list($request->getStatus(), $request->getPerPage());
    }

    /**
     * Store a newly created post with platforms
     */
    public function store(OrderStoreRequest $request): JsonResponse
    {
        return $this->orderService->create($request->safe()->toArray());
    }

    /**
     * Update an existing order by its id
     */
    public function update(OrderUpdateRequest $request): JsonResponse
    {
        return $this->orderService->update($request->getId(), $request->safe()->except('order'));
    }

    /**
     * Delete an existing order by its id
     */
    public function destroy(OrderDeleteRequest $request): JsonResponse
    {
        return $this->orderService->delete($request->getId());
    }

    /**
     * Cancels an existing order by its id
     */
    public function cancel(OrderUpdateStatusRequest $request): JsonResponse
    {
        return $this->orderService->update($request->getId(), ['status' => Order::CANCELLED]);
    }

    /**
     * Confirms an existing order by its id
     */
    public function confirm(OrderUpdateStatusRequest $request): JsonResponse
    {
        return $this->orderService->update($request->getId(), ['status' => Order::CONFIRMED]);
    }
}
