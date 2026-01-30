<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\AbstractRepository;
use App\Architecture\Repositories\Interfaces\IStockTransferRepository;
use Illuminate\Database\Eloquent\Model;

class StockTransferRepository extends AbstractRepository implements IStockTransferRepository
{
    /**
     * @param array $data
     * @return Model
     */
    public function store(array $data)
    {
        return $this->create($data);
    }
}
