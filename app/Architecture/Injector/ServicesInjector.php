<?php

namespace App\Architecture\Injector;

use App\Architecture\Services\Classes\InventoryItemService;
use App\Architecture\Services\Classes\StockTransferService;
use App\Architecture\Services\Interfaces\IInventoryItemService;
use App\Architecture\Services\Interfaces\IStockTransferService;
use Illuminate\Support\ServiceProvider;

class ServicesInjector extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IStockTransferService::class, StockTransferService::class);
        $this->app->bind(IInventoryItemService::class, InventoryItemService::class);
    }
}
