<?php

namespace App\Architecture\Injector;

use App\Architecture\Responder\ApiHttpResponder;
use App\Architecture\Responder\IApiHttpResponder;
use Illuminate\Support\ServiceProvider;

class ResponderInjector extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IApiHttpResponder::class, ApiHttpResponder::class);
    }
}
