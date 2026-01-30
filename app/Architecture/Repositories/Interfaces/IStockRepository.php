<?php

namespace App\Architecture\Repositories\Interfaces;

use App\Models\StockTransfer;

interface IStockRepository
{
    /**
     * @param array $conditions
     */
    public function first(array $conditions);

    /**
     * @param array $data
     */
    public function create(array $data);
}
