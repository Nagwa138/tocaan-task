<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\AbstractRepository;
use App\Architecture\Repositories\Interfaces\IStockTransferRepository;
use App\Architecture\Repositories\Interfaces\IWarehouseRepository;
use App\Models\StockTransfer;

class WarehouseRepository extends AbstractRepository implements IWarehouseRepository
{
}
