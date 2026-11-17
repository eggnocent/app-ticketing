<?php

namespace App\Modules\EventModule\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required', 'string', 'max:120', 'alpha_dash',
                Rule::unique('categories', 'slug')->ignore($this->route('category')),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
