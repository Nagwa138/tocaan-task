<?php

namespace App\Architecture\Repositories\Interfaces;

use App\Models\StockTransfer;

interface IWarehouseRepository
{
    /**
     * @param array $conditions
     */
    public function first(array $conditions);
}
