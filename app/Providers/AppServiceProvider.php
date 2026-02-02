<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use App\Policies\OrderPolicy;
//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Order::class => OrderPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Order::observe(OrderObserver::class);
    }
}
