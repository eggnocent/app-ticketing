<?php

namespace App\Modules\RegistrationModule\Requests;

use App\Modules\EventModule\Contracts\EventInfoContract;
use App\Modules\TicketModule\Contracts\TicketInventoryContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('items') || ! is_array($this->input('quantities'))) {
            return;
        }

        $items = [];

        foreach ($this->input('quantities', []) as $ticketId => $quantity) {
            if ((int) $quantity > 0) {
                $items[] = [
                    'ticket_id' => $ticketId,
                    'quantity' => (int) $quantity,
                ];
            }
        }

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:10'],
            'items.*.ticket_id' => ['required', 'uuid', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $inventory = app(TicketInventoryContract::class);
                $events = app(EventInfoContract::class);
                $eventIds = [];

                foreach ($this->input('items', []) as $index => $item) {
                    $summary = $inventory->getSummary($item['ticket_id']);

                    if (! $summary) {
                        $validator->errors()->add(
                            "items.{$index}.ticket_id",
                            'Tiket tidak ditemukan.',
                        );

                        continue;
                    }

                    $eventIds[$summary['event_id']] = true;
                }

                if (count($eventIds) > 1) {
                    $validator->errors()->add(
                        'items',
                        'Satu checkout hanya boleh berisi tiket dari satu event.',
                    );

                    return;
                }

                if (count($eventIds) === 1 && ! $events->isPurchasable(array_key_first($eventIds))) {
                    $validator->errors()->add(
                        'items',
                        'Event tidak tersedia untuk pembelian.',
                    );
                }
            },
        ];
    }
}
