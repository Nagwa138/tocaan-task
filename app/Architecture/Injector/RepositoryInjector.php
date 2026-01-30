<?php

namespace App\Architecture\Injector;

use App\Architecture\Repositories\Classes\InventoryItemRepository;
use App\Architecture\Repositories\Classes\StockRepository;
use App\Architecture\Repositories\Classes\StockTransferRepository;
use App\Architecture\Repositories\Classes\WarehouseRepository;
use App\Architecture\Repositories\Interfaces\IInventoryItemRepository;
use App\Architecture\Repositories\Interfaces\IStockRepository;
use App\Architecture\Repositories\Interfaces\IStockTransferRepository;
use App\Architecture\Repositories\Interfaces\IWarehouseRepository;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Support\ServiceProvider;

class RepositoryInjector extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IStockTransferRepository::class, function ($app) {
            return new StockTransferRepository($app->make(StockTransfer::class));
        });
        $this->app->singleton(IInventoryItemRepository::class, function ($app) {
            return new InventoryItemRepository($app->make(InventoryItem::class));
        });
        $this->app->singleton(IWarehouseRepository::class, function ($app) {
            return new WarehouseRepository($app->make(Warehouse::class));
        });
        $this->app->singleton(IStockRepository::class, function ($app) {
            return new StockRepository($app->make(Stock::class));
        });
    }
}
