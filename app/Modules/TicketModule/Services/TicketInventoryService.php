<?php

namespace App\Modules\TicketModule\Services;

use App\Modules\TicketModule\Contracts\TicketInventoryContract;
use App\Modules\TicketModule\Exceptions\TicketNotAvailableException;
use App\Modules\TicketModule\Exceptions\TicketQuotaExceededException;
use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class TicketInventoryService implements TicketInventoryContract
{
    public function getSummary(string $ticketId): ?array
    {
        $ticket = Ticket::query()->find($ticketId);

        if (! $ticket) {
            return null;
        }

        return [
            'id' => $ticket->id,
            'event_id' => $ticket->event_id,
            'name' => $ticket->name,
            'price_rupiah' => $ticket->price_rupiah,
            'initial_quota' => $ticket->initial_quota,
            'remaining_quota' => $ticket->remaining_quota,
            'sales_start' => $ticket->sales_start?->toIso8601String(),
            'sales_end' => $ticket->sales_end?->toIso8601String(),
            'status' => $ticket->status,
            'is_on_sale' => $ticket->isOnSale(),
        ];
    }

    public function getEventTickets(string $eventId): array
    {
        return Ticket::query()
            ->where('event_id', $eventId)
            ->where('status', 'active')
            ->orderBy('price_rupiah')
            ->get()
            ->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'event_id' => $ticket->event_id,
                'name' => $ticket->name,
                'price_rupiah' => $ticket->price_rupiah,
                'remaining_quota' => $ticket->remaining_quota,
                'sales_start' => $ticket->sales_start?->toIso8601String(),
                'sales_end' => $ticket->sales_end?->toIso8601String(),
                'is_on_sale' => $ticket->isOnSale(),
            ])
            ->all();
    }

    public function reserve(string $ticketId, int $quantity): array
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('TicketInventoryContract::reserve() harus dipanggil di dalam transaksi database.');
        }

        if ($quantity < 1) {
            throw new InvalidArgumentException('Jumlah tiket minimal 1.');
        }

        $ticket = Ticket::query()->lockForUpdate()->find($ticketId);

        if (! $ticket) {
            throw new TicketNotAvailableException('Tiket tidak ditemukan.');
        }

        if ($ticket->status !== 'active') {
            throw new TicketNotAvailableException('Tiket sedang tidak dijual.');
        }

        if (! $ticket->isOnSale()) {
            throw new TicketNotAvailableException('Periode penjualan tiket tidak aktif.');
        }

        if ($ticket->remaining_quota < $quantity) {
            throw new TicketQuotaExceededException('Sisa tiket tidak mencukupi.');
        }

        $ticket->update([
            'remaining_quota' => $ticket->remaining_quota - $quantity,
            'updated_by' => Auth::id(),
        ]);

        return [
            'id' => $ticket->id,
            'event_id' => $ticket->event_id,
            'name' => $ticket->name,
            'price_rupiah' => $ticket->price_rupiah,
            'quantity' => $quantity,
        ];
    }

    public function release(string $ticketId, int $quantity): void
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('TicketInventoryContract::release() harus dipanggil di dalam transaksi database.');
        }

        if ($quantity < 1) {
            throw new InvalidArgumentException('Jumlah tiket minimal 1.');
        }

        $ticket = Ticket::query()->lockForUpdate()->find($ticketId);

        if (! $ticket) {
            throw new TicketNotAvailableException('Tiket tidak ditemukan.');
        }

        $ticket->update([
            'remaining_quota' => min($ticket->initial_quota, $ticket->remaining_quota + $quantity),
            'updated_by' => Auth::id(),
        ]);
    }
}
