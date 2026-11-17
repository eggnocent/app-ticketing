<?php

namespace App\Modules\RegistrationModule\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class TicketPurchasedEvent implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(public readonly string $registrationId) {}
}
