<?php

namespace App\Modules\EventModule\Contracts;

interface EventInfoContract
{
    public function getSummary(string $eventId): ?array;

    /**
     * @return array<int, array{id: string, title: string, slug: string, status: string}>
     */
    public function getEventOptions(): array;

    public function isPurchasable(string $eventId): bool;
}
