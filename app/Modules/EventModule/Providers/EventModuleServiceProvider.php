<?php

namespace App\Modules\EventModule\Providers;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\EventModule\Services\EventInfoService;
use Illuminate\Support\ServiceProvider;

class EventModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventInfoContract::class, EventInfoService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Views', 'event');
    }
}
