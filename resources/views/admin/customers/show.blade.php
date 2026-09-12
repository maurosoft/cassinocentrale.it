@extends('layouts.admin')

@section('title', 'Cliente · '.$customer->fullName())

@section('content')
    <a href="{{ route('admin.customers.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm text-ink-light hover:text-clay-600"><x-icon name="arrow-left" class="h-4 w-4"/> Torna ai clienti</a>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-clay-50 font-serif text-lg font-semibold text-clay-700">{{ \Illuminate\Support\Str::substr($customer->first_name, 0, 1) }}{{ \Illuminate\Support\Str::substr($customer->last_name, 0, 1) }}</div>
                <div>
                    <h2 class="font-serif text-lg text-ink">{{ $customer->fullName() }}</h2>
                    <p class="text-xs text-ink-soft">Cliente dal {{ $customer->created_at->format('m/Y') }}</p>
                </div>
            </div>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-ink-soft">Email</dt><dd class="text-ink">{{ $customer->email ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-soft">Telefono</dt><dd class="text-ink">{{ $customer->phone ?: '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-ink-soft">Prenotazioni</dt><dd class="text-ink">{{ $customer->bookings->count() }}</dd></div>
            </dl>
            <a href="{{ route('admin.bookings.create') }}" class="btn-primary mt-5 w-full"><x-icon name="plus" class="h-4 w-4"/> Nuova prenotazione</a>
        </div>

        <div class="lg:col-span-2">
            <h3 class="mb-3 font-serif text-lg text-ink">Storico prenotazioni</h3>
            <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-cream-300 bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                        <tr><th class="px-4 py-3">Periodo</th><th class="px-4 py-3">Camere</th><th class="px-4 py-3">Stato</th><th class="px-4 py-3 text-right">Totale</th></tr>
                    </thead>
                    <tbody class="divide-y divide-cream-200">
                        @forelse ($customer->bookings as $b)
                            <tr class="cursor-pointer hover:bg-cream-50" onclick="window.location='{{ route('admin.bookings.show', $b) }}'">
                                <td class="px-4 py-3 text-ink-light">{{ $b->check_in->format('d/m/y') }} → {{ $b->check_out->format('d/m/y') }}</td>
                                <td class="px-4 py-3 text-ink-light">{{ $b->rooms->map(fn ($r) => $r->room?->number_name)->filter()->join(', ') }}</td>
                                <td class="px-4 py-3">{{ $b->statusLabel() }}</td>
                                <td class="px-4 py-3 text-right">€{{ number_format($b->total_price, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-ink-soft">Nessuna prenotazione.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
