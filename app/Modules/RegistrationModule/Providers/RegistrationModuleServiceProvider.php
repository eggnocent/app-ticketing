<?php

namespace App\Modules\RegistrationModule\Providers;

use Illuminate\Support\ServiceProvider;

class RegistrationModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Views', 'registration');
    }
}
