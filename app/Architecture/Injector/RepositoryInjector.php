<?php

namespace App\Architecture\Injector;

use App\Architecture\Repositories\Classes\OrderRepository;
use App\Architecture\Repositories\Classes\PaymentRepository;
use App\Architecture\Repositories\Interfaces\IOrderRepository;
use App\Architecture\Repositories\Interfaces\IPaymentRepository;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\ServiceProvider;

class RepositoryInjector extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IOrderRepository::class, function ($app) {
            return new OrderRepository($app->make(Order::class));
        });
        $this->app->singleton(IPaymentRepository::class, function ($app) {
            return new PaymentRepository($app->make(Payment::class));
        });
    }
}
