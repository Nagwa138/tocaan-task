<?php

namespace App\Architecture\Services\Classes;

use App\Architecture\Repositories\Interfaces\IOrderRepository;
use App\Architecture\Responder\IApiHttpResponder;
use App\Architecture\Services\Interfaces\IOrderService;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class OrderService implements IOrderService
{
    /**
     * @param IOrderRepository $orderRepository
     * @param IApiHttpResponder $apiHttpResponder
     */
    public function __construct(
        public IOrderRepository $orderRepository,
        public IApiHttpResponder $apiHttpResponder
    )
    {}

    /**
     * Create post with platforms
     *
     * @param array $data
     * @return JsonResponse
     */
    public function create(array $data): JsonResponse
    {
        try {
            $order = $this->orderRepository->create(array_merge($data, [
                'user_id' => auth()->id(),
                'total_amount' => $this->calculateTotalPrice($data['items']),
            ]))->fresh();

            return $this->apiHttpResponder->sendSuccess((new OrderResource($order->load('user')))->toArray(request()), Response::HTTP_CREATED);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    public function list(string $status = null, int $perPage = 10): JsonResponse
    {
        try {
            $filters = $status ? ['status' => $status, 'user_id' => auth()->id()] : ['user_id' => auth()->id()];
            $orders = $this->orderRepository->listBy($filters, $perPage);
            return $this->apiHttpResponder->sendSuccess((new OrderResource($orders))->toArray(request()), Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    public function update(int $id, array $data): JsonResponse
    {
        try {
            $order = $this->orderRepository->first(['id' => $id]);

            // Business rule: Cannot update order if it has payments
            if ($order->payments()->exists() && Arr::has($data, 'status')) {
                return $this->apiHttpResponder->sendError(
                    'Cannot update order status after payment has been processed',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if (($order->status !== Order::PENDING) && Arr::has($data, 'status') && ($data['status'] !== Order::PENDING)) {
                return $this->apiHttpResponder->sendError(
                    'Only pending orders can be cancelled or confirmed',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if (Arr::has($data, 'items')) {
                $data['total_amount'] = $this->calculateTotalPrice($data['items']);
            }

            $this->orderRepository->update(['id' => $id], $data);

            return $this->apiHttpResponder->sendSuccess((new OrderResource($order->fresh()->load('user')))->toArray(request()));

        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $order = $this->orderRepository->first(['id' => $id]);

            // Business rule: Cannot delete order if it has payments
            if ($order->payments()->exists()) {
                return $this->apiHttpResponder->sendError(
                    'Cannot delete order after payment has been processed',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $this->orderRepository->delete($id);
            return $this->apiHttpResponder->sendSuccess(['message' => 'Order deleted successfully!']);
        } catch (\Throwable $exception) {
            return $this->apiHttpResponder->sendError($exception->getMessage(), $exception->getCode());
        }
    }

    private function calculateTotalPrice(array $items): float
    {
        $items = collect($items);

        return $items->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });
    }
}
