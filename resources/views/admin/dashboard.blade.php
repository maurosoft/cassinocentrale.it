@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <p class="text-sm text-ink-light">Ciao {{ auth()->user()->name }}, ecco la situazione di oggi ({{ now()->translatedFormat('d F Y') }}).</p>

    @php($canOps = auth()->user()->isSuperadmin() || auth()->user()->hasRole('reception'))

    {{-- Numeri principali (cliccabili) --}}
    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php($cards = [
            ['label' => 'Arrivi oggi', 'value' => $stats['arrivals_today'], 'icon' => 'calendar', 'to' => $canOps ? route('admin.bookings.index') : null],
            ['label' => 'Partenze oggi', 'value' => $stats['departures_today'], 'icon' => 'external', 'to' => $canOps ? route('admin.bookings.index') : null],
            ['label' => 'Camere occupate', 'value' => $stats['occupied'].' / '.$stats['total_rooms'], 'icon' => 'bed', 'to' => $canOps ? route('admin.calendar.index') : null],
            ['label' => 'Camere libere', 'value' => $stats['free'], 'icon' => 'check', 'to' => $canOps ? route('admin.calendar.index') : null],
        ])
        @foreach ($cards as $c)
            <{{ $c['to'] ? 'a' : 'div' }} @if ($c['to']) href="{{ $c['to'] }}" @endif class="block rounded-2xl border border-cream-300 bg-white p-5 {{ $c['to'] ? 'transition hover:border-clay-300 hover:shadow-md' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-ink-soft">{{ $c['label'] }}</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-clay-50 text-clay-600"><x-icon :name="$c['icon']" class="h-5 w-5"/></span>
                </div>
                <p class="mt-3 font-serif text-3xl font-semibold text-ink">{{ $c['value'] }}</p>
            </{{ $c['to'] ? 'a' : 'div' }}>
        @endforeach
    </div>

    @if ($canOps)
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('admin.bookings.create') }}" class="btn-primary !py-2"><x-icon name="plus" class="h-4 w-4"/> Nuova prenotazione</a>
            <a href="{{ route('admin.calendar.index') }}" class="btn-outline !py-2">Apri il calendario</a>
            <a href="{{ route('admin.customers.index') }}" class="btn-ghost !py-2">Clienti</a>
        </div>
    @endif

    {{-- Occupazione + periodi --}}
    <div class="mt-4 grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <span class="text-sm text-ink-soft">Occupazione oggi</span>
            <p class="mt-2 font-serif text-3xl font-semibold text-ink">{{ $stats['occupancy'] }}%</p>
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-cream-200">
                <div class="h-full rounded-full bg-clay-500" style="width: {{ $stats['occupancy'] }}%"></div>
            </div>
        </div>
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <span class="text-sm text-ink-soft">Arrivi questa settimana</span>
            <p class="mt-2 font-serif text-3xl font-semibold text-ink">{{ $stats['week'] }}</p>
        </div>
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <span class="text-sm text-ink-soft">Arrivi questo mese</span>
            <p class="mt-2 font-serif text-3xl font-semibold text-ink">{{ $stats['month'] }}</p>
        </div>
    </div>

    {{-- Liste --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Prossimi arrivi</h2>
            @forelse ($upcoming as $b)
                <div class="mt-3 flex items-center justify-between border-t border-cream-200 pt-3 text-sm">
                    <div>
                        <p class="font-medium text-ink">{{ $b->guest_name }}</p>
                        <p class="text-xs text-ink-soft">{{ $b->rooms->map(fn ($r) => $r->room?->number_name)->filter()->join(', ') }} · {{ $b->nights() }} notti</p>
                    </div>
                    <div class="text-right">
                        <p class="text-ink">{{ $b->check_in->format('d/m') }} → {{ $b->check_out->format('d/m') }}</p>
                        <p class="text-xs text-ink-soft">{{ $b->statusLabel() }}</p>
                    </div>
                </div>
            @empty
                <p class="mt-3 text-sm text-ink-soft">Nessun arrivo in programma.</p>
            @endforelse
        </div>

        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-serif text-lg text-ink">Ultime prenotazioni</h2>
                @if (auth()->user()->isSuperadmin() || auth()->user()->hasRole('reception'))
                    <a href="{{ route('admin.bookings.index') }}" class="text-xs font-medium text-clay-600 hover:text-clay-700">Tutte →</a>
                @endif
            </div>
            @forelse ($latest as $b)
                <div class="mt-3 flex items-center justify-between border-t border-cream-200 pt-3 text-sm">
                    <div>
                        <p class="font-medium text-ink">{{ $b->guest_name }}</p>
                        <p class="text-xs text-ink-soft">{{ $b->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-medium
                        @class([
                            'bg-sage-100 text-sage-700' => $b->status === \App\Models\Booking::STATUS_CONFIRMED,
                            'bg-cream-200 text-ink-light' => $b->status === \App\Models\Booking::STATUS_DRAFT,
                            'bg-red-100 text-red-700' => $b->status === \App\Models\Booking::STATUS_CANCELLED,
                            'bg-clay-50 text-clay-700' => $b->status === \App\Models\Booking::STATUS_COMPLETED,
                        ])">{{ $b->statusLabel() }}</span>
                </div>
            @empty
                <p class="mt-3 text-sm text-ink-soft">Ancora nessuna prenotazione.</p>
            @endforelse
        </div>
    </div>
@endsection
