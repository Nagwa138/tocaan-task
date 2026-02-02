<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\AbstractRepository;
use App\Architecture\Repositories\Interfaces\IOrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository extends AbstractRepository implements IOrderRepository
{
    /**
     * @param array $conditions
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listBy(array $conditions = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->prepareQuery()->where($conditions)->with('user')->paginate($perPage);
    }
}
