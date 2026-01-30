<?php

namespace App\Architecture\Services\Interfaces;

use Illuminate\Http\JsonResponse;

interface IStockTransferService
{
    /**
     * @param array $data
     */
    public function create(array $data): JsonResponse;
}
