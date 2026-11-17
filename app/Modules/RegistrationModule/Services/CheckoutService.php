<?php

namespace App\Modules\RegistrationModule\Services;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\RegistrationModule\Events\TicketPurchasedEvent;
use App\Modules\RegistrationModule\Exceptions\CheckoutFailedException;
use App\Modules\RegistrationModule\Models\IssuedTicket;
use App\Modules\RegistrationModule\Models\Registration;
use App\Modules\RegistrationModule\Models\RegistrationTicket;
use App\Modules\TicketModule\Contracts\TicketInventoryContract;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        private readonly EventInfoContract $events,
        private readonly TicketInventoryContract $inventory,
    ) {}

    /**
     * @param  array<int, array{ticket_id: string, quantity: int}>  $items
     */
    public function process(string $userId, array $items, ?string $idempotencyKey = null): Registration
    {
        if ($items === []) {
            throw new CheckoutFailedException('Pilih minimal satu tiket.');
        }

        if ($idempotencyKey !== null) {
            $existing = $this->findExistingRegistration($userId, $idempotencyKey);

            if ($existing) {
                return $existing;
            }
        }

        $items = collect($items)
            ->map(fn (array $item) => [
                'ticket_id' => (string) $item['ticket_id'],
                'quantity' => (int) $item['quantity'],
            ])
            ->sortBy('ticket_id')
            ->values()
            ->all();

        if (count(array_unique(array_column($items, 'ticket_id'))) !== count($items)) {
            throw new CheckoutFailedException('Jenis tiket tidak boleh duplikat.');
        }

        try {
            return DB::transaction(function () use ($userId, $items, $idempotencyKey): Registration {
                $prepared = [];
                $total = 0;
                $eventId = null;

                foreach ($items as $item) {
                    if ($item['quantity'] < 1 || $item['quantity'] > 10) {
                        throw new CheckoutFailedException('Jumlah tiket harus antara 1 dan 10.');
                    }

                    $snapshot = $this->inventory->reserve($item['ticket_id'], $item['quantity']);

                    if ($eventId === null) {
                        $eventId = $snapshot['event_id'];
                    } elseif ($eventId !== $snapshot['event_id']) {
                        throw new CheckoutFailedException('Satu checkout hanya untuk satu event.');
                    }

                    if (! $this->events->isPurchasable($eventId)) {
                        throw new CheckoutFailedException('Event tidak tersedia untuk pembelian.');
                    }

                    $subtotal = $snapshot['price_rupiah'] * $item['quantity'];

                    $prepared[] = [
                        'snapshot' => $snapshot,
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal,
                    ];

                    $total += $subtotal;
                }

                $registration = Registration::create([
                    'user_id' => $userId,
                    'invoice_number' => $this->uniqueInvoiceNumber(),
                    'idempotency_key' => $idempotencyKey,
                    'total_rupiah' => $total,
                    'status' => 'completed',
                    'confirmed_at' => now(),
                ]);

                foreach ($prepared as $item) {
                    $line = RegistrationTicket::create([
                        'registration_id' => $registration->id,
                        'ticket_id' => $item['snapshot']['id'],
                        'quantity' => $item['quantity'],
                        'unit_price_rupiah' => $item['snapshot']['price_rupiah'],
                        'subtotal_rupiah' => $item['subtotal'],
                    ]);

                    for ($i = 0; $i < $item['quantity']; $i++) {
                        IssuedTicket::create([
                            'registration_ticket_id' => $line->id,
                            'ticket_code' => $this->uniqueTicketCode(),
                            'status' => 'valid',
                        ]);
                    }
                }

                TicketPurchasedEvent::dispatch($registration->id);

                return $registration->load('registrationTickets.issuedTickets');
            }, attempts: 3);
        } catch (UniqueConstraintViolationException $exception) {
            if ($idempotencyKey !== null) {
                $existing = $this->findExistingRegistration($userId, $idempotencyKey);

                if ($existing) {
                    return $existing;
                }
            }

            throw $exception;
        }
    }

    private function findExistingRegistration(string $userId, string $idempotencyKey): ?Registration
    {
        return Registration::query()
            ->where('user_id', $userId)
            ->where('idempotency_key', $idempotencyKey)
            ->with('registrationTickets.issuedTickets')
            ->first();
    }

    private function uniqueInvoiceNumber(): string
    {
        for ($attempt = 0; $attempt < 8; $attempt++) {
            $candidate = 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));

            if (! Registration::query()->where('invoice_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new CheckoutFailedException('Gagal membuat nomor invoice unik.');
    }

    private function uniqueTicketCode(): string
    {
        for ($attempt = 0; $attempt < 8; $attempt++) {
            $candidate = 'TKT-'.Str::upper(Str::random(12));

            if (! IssuedTicket::query()->where('ticket_code', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new CheckoutFailedException('Gagal membuat kode tiket unik.');
    }
}
