<?php

namespace App\Modules\RegistrationModule\Listeners;

use App\Modules\RegistrationModule\Events\TicketPurchasedEvent;
use Illuminate\Support\Facades\Log;

class LogTicketPurchase
{
    public function handle(TicketPurchasedEvent $event): void
    {
        Log::info('Ticket purchase completed.', [
            'registration_id' => $event->registrationId,
        ]);
    }
}
