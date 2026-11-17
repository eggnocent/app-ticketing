<?php

namespace App\Modules\TicketModule\Contracts;

interface TicketInventoryContract
{
    public function getSummary(string $ticketId): ?array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getEventTickets(string $eventId): array;

    /**
     * Must be called inside a database transaction.
     *
     * @return array{id: string, event_id: string, name: string, price_rupiah: int, quantity: int}
     */
    public function reserve(string $ticketId, int $quantity): array;

    public function release(string $ticketId, int $quantity): void;
}
