<?php

namespace App\Architecture\Repositories\Interfaces;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface IPaymentRepository
{
    /**
     * Create new post
     *
     * @param array $data
     * @return Payment|Model
     */
    public function create(array $data): Payment|Model;

    /**
     * @param array $conditions
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(array $conditions = [], int $perPage = 10);

    public function existOrderPaymentRecently(string $orderId): bool;
}
