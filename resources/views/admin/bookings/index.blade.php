@extends('layouts.admin')

@section('title', 'Prenotazioni')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-ink-light">Gestione prenotazioni</p>
        <a href="{{ route('admin.bookings.create') }}" class="btn-primary !py-2.5"><x-icon name="plus" class="h-4 w-4"/> Nuova prenotazione</a>
    </div>

    {{-- Filtri --}}
    <form method="GET" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-cream-300 bg-white p-4">
        <div>
            <label class="block text-xs font-medium text-ink-soft">Cerca</label>
            <input name="q" value="{{ request('q') }}" class="mt-1 rounded-lg border-cream-300 text-sm focus:border-clay-500 focus:ring-clay-500" placeholder="Nome, email, codice">
        </div>
        <div>
            <label class="block text-xs font-medium text-ink-soft">Stato</label>
            <select name="status" class="mt-1 rounded-lg border-cream-300 text-sm focus:border-clay-500 focus:ring-clay-500">
                <option value="">Tutti</option>
                @foreach (\App\Models\Booking::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-ink-soft">Camera</label>
            <select name="room" class="mt-1 rounded-lg border-cream-300 text-sm focus:border-clay-500 focus:ring-clay-500">
                <option value="">Tutte</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected(request('room') == $room->id)>{{ $room->number_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary !py-2.5">Filtra</button>
        @if (request()->hasAny(['q','status','room']))
            <a href="{{ route('admin.bookings.index') }}" class="btn-ghost !py-2.5">Azzera</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-4 py-3">Ospite</th>
                        <th class="px-4 py-3">Camere</th>
                        <th class="px-4 py-3">Periodo</th>
                        <th class="px-4 py-3">Totale</th>
                        <th class="px-4 py-3">Stato</th>
                        <th class="px-4 py-3 text-right">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-200">
                    @forelse ($bookings as $b)
                        <tr class="hover:bg-cream-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-ink">{{ $b->guest_name }}</p>
                                <p class="text-xs text-ink-soft">{{ $b->guest_email }}</p>
                            </td>
                            <td class="px-4 py-3 text-ink-light">{{ $b->rooms->map(fn ($r) => $r->room?->number_name)->filter()->join(', ') ?: '—' }}</td>
                            <td class="px-4 py-3 text-ink-light">{{ $b->check_in->format('d/m/y') }} → {{ $b->check_out->format('d/m/y') }}<span class="block text-xs text-ink-soft">{{ $b->nights() }} notti</span></td>
                            <td class="px-4 py-3">€{{ number_format($b->total_price, 2, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium @class([
                                    'bg-sage-100 text-sage-700' => $b->status === \App\Models\Booking::STATUS_CONFIRMED,
                                    'bg-cream-200 text-ink-light' => $b->status === \App\Models\Booking::STATUS_DRAFT,
                                    'bg-red-100 text-red-700' => $b->status === \App\Models\Booking::STATUS_CANCELLED,
                                    'bg-clay-50 text-clay-700' => $b->status === \App\Models\Booking::STATUS_COMPLETED,
                                ])">{{ $b->statusLabel() }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.bookings.show', $b) }}" class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-clay-600 hover:bg-cream-100"><x-icon name="eye" class="h-4 w-4"/> Dettaglio</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-ink-soft">Nessuna prenotazione trovata.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
@endsection
