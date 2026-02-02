<?php

namespace App\Architecture\Services\Interfaces;

use Illuminate\Http\JsonResponse;

interface IPaymentService
{
    /**
     * List payments with filter
     *
     * @param array $filters
     * @param int $perPage
     * @return JsonResponse
     */
    public function list(array $filters = [], int $perPage = 10): JsonResponse;

    /**
     * Process order payment
     *
     * @param string $orderId
     * @param string $paymentMethod
     * @return JsonResponse
     */
    public function process(string $orderId, string $paymentMethod): JsonResponse;
}
