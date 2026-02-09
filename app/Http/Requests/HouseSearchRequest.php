<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HouseSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'storeys' => ['nullable', 'integer', 'min:0'],
            'garages' => ['nullable', 'integer', 'min:0'],
            'price_from' => ['nullable', 'integer', 'min:0'],
            'price_to' => ['nullable', 'integer', 'min:0'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
        ];
    }
}
