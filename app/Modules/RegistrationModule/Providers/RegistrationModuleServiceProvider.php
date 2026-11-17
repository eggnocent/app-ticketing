<?php

namespace App\Modules\RegistrationModule\Providers;

use App\Modules\RegistrationModule\Events\TicketPurchasedEvent;
use App\Modules\RegistrationModule\Listeners\LogTicketPurchase;
use Illuminate\Support\Facades\Event;
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

        Event::listen(TicketPurchasedEvent::class, LogTicketPurchase::class);
    }
}
