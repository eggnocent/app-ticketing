<?php

namespace App\Modules\EventModule\Contracts;

interface EventInfoContract
{
    public function getSummary(string $eventId): ?array;

    public function isPurchasable(string $eventId): bool;
}
