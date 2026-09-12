@extends('layouts.admin')

@section('title', 'Nuova prenotazione')

@section('content')
    <a href="{{ route('admin.bookings.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm text-ink-light hover:text-clay-600"><x-icon name="arrow-left" class="h-4 w-4"/> Torna alle prenotazioni</a>

    @if (session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"><ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.bookings.store') }}" class="grid gap-6 lg:grid-cols-2">
        @csrf

        {{-- Cliente --}}
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Cliente</h2>
            <p class="mt-1 text-sm text-ink-light">Scegli un cliente già registrato oppure inserisci i dati di uno nuovo.</p>

            @if ($customers->isNotEmpty())
                <div class="mt-4">
                    <label class="block text-sm font-medium text-ink">Cliente esistente</label>
                    <select id="existing-customer" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        <option value="">— Nuovo cliente —</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}"
                                data-first="{{ $c->first_name }}" data-last="{{ $c->last_name }}"
                                data-email="{{ $c->email }}" data-phone="{{ $c->phone }}">
                                {{ $c->fullName() }}@if ($c->email) — {{ $c->email }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Nome *</label>
                    <input name="first_name" id="cust-first" value="{{ old('first_name') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Cognome</label>
                    <input name="last_name" id="cust-last" value="{{ old('last_name') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Email</label>
                    <input type="email" name="email" id="cust-email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Telefono</label>
                    <input name="phone" id="cust-phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
            </div>
            <p class="mt-2 text-xs text-ink-soft">Email e telefono servono per inviare le conferme (le notifiche automatiche arriveranno con la Fase 4).</p>
        </div>

        {{-- Soggiorno --}}
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Soggiorno</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Arrivo *</label>
                    <input type="date" name="check_in" value="{{ old('check_in') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Partenza *</label>
                    <input type="date" name="check_out" value="{{ old('check_out') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Camera *</label>
                    <select name="room_id" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>Camera {{ $room->number_name }}@if ($room->name) · {{ $room->name }}@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Ospiti *</label>
                    <select name="guests" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @for ($g = 1; $g <= 4; $g++)<option value="{{ $g }}" @selected(old('guests', 2) == $g)>{{ $g }}</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Stato *</label>
                    <select name="status" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @foreach (\App\Models\Booking::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', 'confirmed') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-ink">Note</label>
                <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ old('notes') }}</textarea>
            </div>
            <label class="mt-4 flex items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="force" value="1" class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                Forza comunque (anche se la camera risulta occupata)
            </label>
            <p class="mt-2 text-xs text-ink-soft">Il prezzo viene calcolato automaticamente (65 € · sconti secondo le regole).</p>
        </div>

        <div class="lg:col-span-2 flex items-center justify-end gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="btn-ghost">Annulla</a>
            <button type="submit" class="btn-primary">Crea prenotazione</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('existing-customer');
            if (!sel) return;
            sel.addEventListener('change', () => {
                const o = sel.options[sel.selectedIndex];
                document.getElementById('cust-first').value = o.dataset.first || '';
                document.getElementById('cust-last').value = o.dataset.last || '';
                document.getElementById('cust-email').value = o.dataset.email || '';
                document.getElementById('cust-phone').value = o.dataset.phone || '';
            });
        });
    </script>
@endsection
