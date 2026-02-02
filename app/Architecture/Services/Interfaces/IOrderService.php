<?php

namespace App\Architecture\Services\Interfaces;

use Illuminate\Http\JsonResponse;

interface IOrderService
{
    /**
     * Create new order
     *
     * @param array $data
     * @return JsonResponse
     */
    public function create(array $data): JsonResponse;

    /**
     * List user's orders with filter by status
     *
     * @param string|null $status
     * @param int $perPage
     * @return JsonResponse
     */
    public function list(string $status = null, int $perPage = 10): JsonResponse;

    /**
     * Update order
     *
     * @param int $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(int $id, array $data): JsonResponse;

    /**
     * Delete an order
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse;
}
