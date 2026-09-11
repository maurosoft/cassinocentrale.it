<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // l'accesso è già filtrato dai ruoli sulle rotte
    }

    public function rules(): array
    {
        return [
            'number_name' => ['required', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:120'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'rules' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'max_guests' => ['required', 'integer', 'min:1', 'max:10'],
            'has_kitchenette' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'services' => ['nullable', 'array'],
            'services.*' => ['integer', 'exists:services,id'],
            'extra_cost' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ];
    }

    public function attributes(): array
    {
        return [
            'number_name' => 'numero/nome camera',
            'base_price' => 'prezzo base',
            'max_guests' => 'ospiti massimi',
        ];
    }
}
