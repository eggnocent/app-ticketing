<?php

namespace App\Modules\TicketModule\Requests;

use App\Modules\TicketModule\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'uuid', 'exists:events,id'],
            'name' => ['required', 'string', 'max:100'],
            'price_rupiah' => ['required', 'integer', 'min:0'],
            'initial_quota' => ['required', 'integer', 'min:0'],
            'sales_start' => ['required', 'date'],
            'sales_end' => ['required', 'date', 'after:sales_start'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $ticket = $this->route('ticket');

                if (! $ticket instanceof Ticket) {
                    return;
                }

                $sold = $ticket->initial_quota - $ticket->remaining_quota;

                if ($sold > 0 && $this->input('event_id') !== $ticket->event_id) {
                    $validator->errors()->add(
                        'event_id',
                        'Event tiket yang sudah terjual tidak bisa dipindahkan.',
                    );
                }

                if ((int) $this->input('initial_quota') < $sold) {
                    $validator->errors()->add(
                        'initial_quota',
                        "Kuota awal tidak boleh kurang dari jumlah tiket terjual ({$sold}).",
                    );
                }
            },
        ];
    }
}
