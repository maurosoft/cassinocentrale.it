@extends('layouts.admin')

@section('title', 'Prenotazione · '.$booking->guest_name)

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-light hover:text-clay-600"><x-icon name="arrow-left" class="h-4 w-4"/> Torna alle prenotazioni</a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-primary !py-2"><x-icon name="edit" class="h-4 w-4"/> Modifica</a>
            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" onsubmit="return confirm('Eliminare definitivamente questa prenotazione?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-outline !py-2 border-red-300 text-red-600 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4"/> Elimina</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-cream-300 bg-white p-5">
                <h2 class="font-serif text-lg text-ink">Ospite</h2>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-ink-soft">Nome</dt><dd class="font-medium text-ink">@if ($booking->customer)<a href="{{ route('admin.customers.show', $booking->customer) }}" class="text-clay-600 hover:text-clay-700">{{ $booking->guest_name }}</a>@else{{ $booking->guest_name }}@endif</dd></div>
                    <div><dt class="text-ink-soft">Codice</dt><dd class="font-medium text-ink">{{ $booking->reference ?? '—' }}</dd></div>
                    <div><dt class="text-ink-soft">Email</dt><dd class="text-ink">{{ $booking->guest_email ?? '—' }}</dd></div>
                    <div><dt class="text-ink-soft">Telefono</dt><dd class="text-ink">{{ $booking->guest_phone ?? '—' }}</dd></div>
                    <div><dt class="text-ink-soft">Ospiti</dt><dd class="text-ink">{{ $booking->number_of_guests }}</dd></div>
                </dl>
                @if ($booking->notes)
                    <div class="mt-4 rounded-lg bg-cream-50 p-3 text-sm text-ink-light"><strong class="text-ink">Note ospite:</strong> {{ $booking->notes }}</div>
                @endif
            </div>

            <div class="rounded-2xl border border-cream-300 bg-white p-5">
                <h2 class="font-serif text-lg text-ink">Soggiorno</h2>
                <div class="mt-4 flex flex-wrap gap-6 text-sm">
                    <div><p class="text-ink-soft">Check-in</p><p class="font-medium text-ink">{{ $booking->check_in->translatedFormat('d F Y') }}</p></div>
                    <div><p class="text-ink-soft">Check-out</p><p class="font-medium text-ink">{{ $booking->check_out->translatedFormat('d F Y') }}</p></div>
                    <div><p class="text-ink-soft">Notti</p><p class="font-medium text-ink">{{ $booking->nights() }}</p></div>
                </div>
                <table class="mt-4 w-full text-left text-sm">
                    <thead class="text-xs uppercase text-ink-soft"><tr><th class="py-2">Camera</th><th class="py-2">€/notte</th><th class="py-2">Notti</th><th class="py-2 text-right">Subtotale</th></tr></thead>
                    <tbody class="divide-y divide-cream-200">
                        @foreach ($booking->rooms as $br)
                            <tr>
                                <td class="py-2">{{ $br->room?->number_name }}@if ($br->room?->name) · {{ $br->room->name }}@endif</td>
                                <td class="py-2">€{{ number_format($br->price_per_night, 2, ',', '.') }}</td>
                                <td class="py-2">{{ $br->nights }}</td>
                                <td class="py-2 text-right">€{{ number_format($br->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-cream-300 font-medium">
                            <td class="py-2" colspan="3">Totale @if ($booking->discount_percent > 0)<span class="text-xs text-ink-soft">(sconto {{ rtrim(rtrim(number_format($booking->discount_percent, 2, ',', ''), '0'), ',') }}%)</span>@endif</td>
                            <td class="py-2 text-right font-serif text-lg text-clay-700">€{{ number_format($booking->total_price, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Gestione stato --}}
        <div class="space-y-5">
            <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="rounded-2xl border border-cream-300 bg-white p-5">
                @csrf @method('PATCH')
                <h2 class="font-serif text-lg text-ink">Gestione rapida</h2>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-ink">Stato prenotazione</label>
                    <select name="status" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @foreach (\App\Models\Booking::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected($booking->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-ink">Pagamento</label>
                    <select name="payment_status" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        <option value="unpaid" @selected($booking->payment_status === 'unpaid')>Da pagare</option>
                        <option value="paid" @selected($booking->payment_status === 'paid')>Pagato</option>
                        <option value="refunded" @selected($booking->payment_status === 'refunded')>Rimborsato</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-ink">Note interne</label>
                    <textarea name="internal_notes" rows="4" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('internal_notes', $booking->internal_notes) }}</textarea>
                </div>
                <button type="submit" class="btn-primary mt-4 w-full">Salva modifiche</button>
            </form>

            <div class="rounded-2xl border border-cream-300 bg-cream-50 p-5 text-sm text-ink-light">
                <p>✉️ L'invio automatico di email e WhatsApp verrà attivato nella Fase 4. Per ora contatta l'ospite direttamente.</p>
            </div>
        </div>
    </div>
@endsection
