<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:4'],
            'rooms' => ['required', 'array', 'min:1', 'max:2'],
            'rooms.*' => ['exists:rooms,slug'],
            'guest_first_name' => ['required', 'string', 'max:80'],
            'guest_last_name' => ['required', 'string', 'max:80'],
            'guest_email' => ['required', 'email', 'max:150'],
            'guest_phone' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'privacy' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'privacy.accepted' => 'Per proseguire devi accettare l\'informativa sulla privacy.',
            'check_out.after' => 'La data di partenza deve essere successiva a quella di arrivo.',
            'rooms.required' => 'Seleziona le camere per il tuo soggiorno.',
            'rooms.*.exists' => 'Una delle camere selezionate non è valida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'guest_first_name' => 'nome',
            'guest_last_name' => 'cognome',
            'guest_email' => 'email',
            'guest_phone' => 'telefono',
            'check_in' => 'data di arrivo',
            'check_out' => 'data di partenza',
        ];
    }
}
