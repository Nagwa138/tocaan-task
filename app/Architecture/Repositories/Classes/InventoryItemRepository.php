<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\AbstractRepository;
use App\Architecture\Repositories\Interfaces\IInventoryItemRepository;
use Illuminate\Support\Arr;

class InventoryItemRepository extends AbstractRepository implements IInventoryItemRepository
{
    /**
     * @param array $filters
     * @param int $perPage
     * @param int $page
     * @return mixed
     */
    public function list(array $filters, int $perPage): mixed
    {
        $query = $this->prepareQuery();

        return $query->warehouseSearch([
            'search' => Arr::get($filters, 'search'),
            'min_price' => Arr::get($filters, 'min_price'),
            'max_price' => Arr::get($filters, 'max_price'),
            'warehouse_id' => Arr::get($filters, 'id'),
        ])
            ->orderBy('name')
            ->paginate($perPage);
    }
}
