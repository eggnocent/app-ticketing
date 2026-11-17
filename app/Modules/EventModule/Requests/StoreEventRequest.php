<?php

namespace App\Modules\EventModule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:180', 'alpha_dash', 'unique:events,slug'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'status' => ['required', 'in:draft,published,cancelled'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
