<?php

namespace App\Architecture\Injector;

use App\Architecture\Services\Classes\OrderService;
use App\Architecture\Services\Classes\PaymentService;
use App\Architecture\Services\Interfaces\IOrderService;
use App\Architecture\Services\Interfaces\IPaymentService;
use Illuminate\Support\ServiceProvider;

class ServicesInjector extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IOrderService::class, OrderService::class);
        $this->app->bind(IPaymentService::class, PaymentService::class);
    }
}
