<?php

namespace App\Architecture\Services\Interfaces;

use Illuminate\Http\JsonResponse;

interface IInventoryItemService
{
    /**
     * @param array $filters
     * @param int $perPage
     * @return JsonResponse
     */
    public function list(array $filters = [], int $perPage = 10): JsonResponse;
}
