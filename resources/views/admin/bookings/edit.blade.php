@extends('layouts.admin')

@section('title', 'Modifica prenotazione')

@section('content')
    @php($br = $booking->rooms->first())
    <a href="{{ route('admin.bookings.show', $booking) }}" class="mb-4 inline-flex items-center gap-1.5 text-sm text-ink-light hover:text-clay-600"><x-icon name="arrow-left" class="h-4 w-4"/> Torna alla scheda</a>

    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"><ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="grid gap-6 lg:grid-cols-2">
        @csrf @method('PUT')

        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Cliente</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Nome *</label>
                    <input name="first_name" value="{{ old('first_name', $booking->customer?->first_name ?? \Illuminate\Support\Str::before($booking->guest_name, ' ')) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Cognome</label>
                    <input name="last_name" value="{{ old('last_name', $booking->customer?->last_name ?? \Illuminate\Support\Str::after($booking->guest_name, ' ')) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Email</label>
                    <input type="email" name="email" value="{{ old('email', $booking->guest_email) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Telefono</label>
                    <input name="phone" value="{{ old('phone', $booking->guest_phone) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Data di nascita</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', optional($booking->customer?->birth_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Luogo di nascita</label>
                    <input name="birth_place" value="{{ old('birth_place', $booking->customer?->birth_place) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Soggiorno</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Arrivo *</label>
                    <input type="date" name="check_in" value="{{ old('check_in', $booking->check_in->format('Y-m-d')) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Partenza *</label>
                    <input type="date" name="check_out" value="{{ old('check_out', $booking->check_out->format('Y-m-d')) }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Camera *</label>
                    <select name="room_id" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id', $br?->room_id) == $room->id)>Camera {{ $room->number_name }}@if ($room->name) · {{ $room->name }}@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Ospiti *</label>
                    <select name="guests" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @for ($g = 1; $g <= 4; $g++)<option value="{{ $g }}" @selected(old('guests', $booking->number_of_guests) == $g)>{{ $g }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Stato *</label>
                    <select name="status" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @foreach (\App\Models\Booking::STATUSES as $key => $label)<option value="{{ $key }}" @selected(old('status', $booking->status) === $key)>{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Pagamento *</label>
                    <select name="payment_status" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        <option value="unpaid" @selected(old('payment_status', $booking->payment_status) === 'unpaid')>Da pagare</option>
                        <option value="paid" @selected(old('payment_status', $booking->payment_status) === 'paid')>Pagato</option>
                        <option value="refunded" @selected(old('payment_status', $booking->payment_status) === 'refunded')>Rimborsato</option>
                    </select>
                </div>
            </div>
            <label class="mt-4 flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="force" value="1" class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"> Forza comunque (ignora il controllo disponibilità)
            </label>
        </div>

        <div class="rounded-2xl border border-cream-300 bg-white p-5 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Note ospite</label>
                    <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('notes', $booking->notes) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Note interne</label>
                    <textarea name="internal_notes" rows="2" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('internal_notes', $booking->internal_notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 flex items-center justify-end gap-3">
            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-ghost">Annulla</a>
            <button type="submit" class="btn-primary">Salva modifiche</button>
        </div>
    </form>
@endsection
