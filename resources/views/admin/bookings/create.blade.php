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
                <div class="relative mt-4">
                    <label class="block text-sm font-medium text-ink">Cerca cliente esistente</label>
                    <input type="text" id="customer-search" autocomplete="off" placeholder="Digita nome, email o telefono..."
                           class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    <div id="customer-results" class="absolute z-20 mt-1 hidden max-h-56 w-full overflow-auto rounded-lg border border-cream-300 bg-white shadow-lg"></div>
                    <p class="mt-1 text-xs text-ink-soft">Oppure lascia vuoto e compila i campi qui sotto per un cliente nuovo.</p>
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
                <div>
                    <label class="block text-sm font-medium text-ink">Data di nascita</label>
                    <input type="date" name="birth_date" id="cust-birthdate" value="{{ old('birth_date') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Luogo di nascita</label>
                    <input name="birth_place" id="cust-birthplace" value="{{ old('birth_place') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
            </div>
            <p class="mt-2 text-xs text-ink-soft">Email e telefono servono per le conferme. Data e luogo di nascita sono utili per il CRM/marketing (facoltativi).</p>
        </div>

        {{-- Soggiorno --}}
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Soggiorno</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Arrivo *</label>
                    <input type="date" name="check_in" value="{{ old('check_in', $prefill['check_in'] ?? '') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Partenza *</label>
                    <input type="date" name="check_out" value="{{ old('check_out', $prefill['check_out'] ?? '') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Camera *</label>
                    <select name="room_id" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id', $prefill['room_id'] ?? '') == $room->id)>Camera {{ $room->number_name }}@if ($room->name) · {{ $room->name }}@endif</option>
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
            const search = document.getElementById('customer-search');
            const results = document.getElementById('customer-results');
            if (!search || !results) return;

            const customers = @json($customersData);

            const fill = (c) => {
                document.getElementById('cust-first').value = c.first || '';
                document.getElementById('cust-last').value = c.last || '';
                document.getElementById('cust-email').value = c.email || '';
                document.getElementById('cust-phone').value = c.phone || '';
                const bd = document.getElementById('cust-birthdate'); if (bd) bd.value = c.birthdate || '';
                const bp = document.getElementById('cust-birthplace'); if (bp) bp.value = c.birthplace || '';
            };

            const render = (list) => {
                if (!list.length) { results.classList.add('hidden'); return; }
                results.innerHTML = list.slice(0, 8).map((c, i) =>
                    `<button type="button" data-i="${i}" class="block w-full px-3 py-2 text-left text-sm hover:bg-cream-100">
                        <span class="font-medium text-ink">${(c.first || '') + ' ' + (c.last || '')}</span>
                        ${c.email ? `<span class="block text-xs text-ink-soft">${c.email}</span>` : ''}
                    </button>`).join('');
                results.dataset.list = JSON.stringify(list.slice(0, 8));
                results.classList.remove('hidden');
            };

            search.addEventListener('input', () => {
                const q = search.value.trim().toLowerCase();
                if (q.length < 2) { results.classList.add('hidden'); return; }
                render(customers.filter((c) => c.label.toLowerCase().includes(q)));
            });

            results.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-i]');
                if (!btn) return;
                const list = JSON.parse(results.dataset.list || '[]');
                const c = list[parseInt(btn.dataset.i, 10)];
                if (c) { fill(c); search.value = (c.first || '') + ' ' + (c.last || ''); }
                results.classList.add('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!results.contains(e.target) && e.target !== search) results.classList.add('hidden');
            });
        });

        // Partenza proposta al giorno successivo all'arrivo.
        document.addEventListener('DOMContentLoaded', () => {
            const ci = document.querySelector('input[name="check_in"]');
            const co = document.querySelector('input[name="check_out"]');
            if (!ci || !co) return;
            const nextDay = (v) => { const d = new Date(v); d.setDate(d.getDate() + 1); return d.toISOString().slice(0, 10); };
            ci.addEventListener('change', () => {
                if (!ci.value) return;
                const n = nextDay(ci.value);
                if (!co.value || co.value <= ci.value) co.value = n;
                co.min = n;
            });
            if (ci.value) co.min = nextDay(ci.value);
        });
    </script>
@endsection
