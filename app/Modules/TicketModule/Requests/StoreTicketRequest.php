<?php

namespace App\Modules\TicketModule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
}
