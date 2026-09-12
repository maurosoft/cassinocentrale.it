@extends('layouts.admin')

@section('title', 'Chiusure e disponibilità')

@section('content')
    <a href="{{ route('admin.calendar.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm text-ink-light hover:text-clay-600"><x-icon name="arrow-left" class="h-4 w-4"/> Torna al calendario</a>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Form nuova chiusura --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-cream-300 bg-white p-5">
                <h2 class="font-serif text-lg text-ink">Chiudi un periodo</h2>
                <p class="mt-1 text-sm text-ink-light">Blocca le prenotazioni per tutta la struttura o per una singola camera.</p>

                @if ($errors->any())
                    <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"><ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                <form method="POST" action="{{ route('admin.closures.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-ink">Camera</label>
                        <select name="room_id" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                            <option value="">Tutto il B&amp;B</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>Camera {{ $room->number_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Dal giorno *</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Al giorno (incluso) *</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Motivo (facoltativo)</label>
                        <input name="reason" value="{{ old('reason') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Manutenzione, ferie...">
                    </div>
                    <button type="submit" class="btn-primary w-full">Aggiungi chiusura</button>
                </form>
            </div>
        </div>

        {{-- Elenco chiusure --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                            <tr>
                                <th class="px-4 py-3">Ambito</th>
                                <th class="px-4 py-3">Periodo</th>
                                <th class="px-4 py-3">Motivo</th>
                                <th class="px-4 py-3 text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-200">
                            @forelse ($closures as $closure)
                                <tr class="hover:bg-cream-50">
                                    <td class="px-4 py-3 font-medium text-ink">{{ $closure->room ? 'Camera '.$closure->room->number_name : 'Tutto il B&B' }}</td>
                                    <td class="px-4 py-3 text-ink-light">{{ $closure->start_date->format('d/m/Y') }} → {{ $closure->end_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-ink-light">{{ $closure->reason ?: '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('admin.closures.destroy', $closure) }}" onsubmit="return confirm('Rimuovere questa chiusura?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded-lg p-2 text-red-500 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4"/></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-ink-soft">Nessuna chiusura impostata. Tutte le camere sono prenotabili.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
