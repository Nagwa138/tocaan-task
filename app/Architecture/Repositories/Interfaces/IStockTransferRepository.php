<?php

namespace App\Architecture\Repositories\Interfaces;

use App\Models\StockTransfer;

interface IStockTransferRepository
{
    /**
     * @param array $conditions
     */
    public function first(array $conditions);

    /**
     * @param array $data
     */
    public function store(array $data);
}
