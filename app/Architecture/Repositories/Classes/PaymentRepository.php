<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\AbstractRepository;
use App\Architecture\Repositories\Interfaces\IPaymentRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository extends AbstractRepository implements IPaymentRepository
{
    public function existOrderPaymentRecently(string $orderId): bool
    {
        return $this->prepareQuery()->where('order_id', $orderId)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->where('status', 'pending')
            ->exists();
    }
}
