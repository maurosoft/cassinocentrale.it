@extends('layouts.app')

@section('title', 'Negozi e attività · '.$bnb['name'])
@section('meta_description', 'Negozi, ristoranti, bar e servizi convenzionati con il B&B Cassino Centrale: sconti e piccoli extra riservati ai nostri ospiti.')

@section('content')
    <section class="bg-cream-50 py-14">
        <div class="container-bnb text-center" data-reveal>
            <span class="eyebrow">Attività</span>
            <h1 class="section-title text-balance">Negozi e attività locali</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink-light">Ristoranti, bar, negozi e servizi amici del B&amp;B: mostrando la conferma di prenotazione hai sconti e piccoli extra riservati a te.</p>
            <a href="{{ route('discover.index') }}" class="btn-outline mt-6">Vedi anche i luoghi turistici →</a>
        </div>
    </section>

    <section class="py-16">
        <div class="container-bnb">
            @if ($conventions->isNotEmpty())
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($conventions as $place)
                        @php($icon = match($place->type) { 'ristorante' => 'utensils', 'bar' => 'coffee', 'negozio' => 'shop', default => 'gift' })
                        <a href="{{ route('discover.show', $place) }}" class="flex flex-col rounded-2xl border border-clay-200 bg-white p-6 card-lift" data-reveal>
                            <div class="flex items-center justify-between">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-clay-50 text-clay-600">
                                    <x-icon :name="$icon" class="h-6 w-6"/>
                                </div>
                                @if ($place->type)<span class="badge-clay">{{ $place->typeLabel() }}</span>@endif
                            </div>
                            <h3 class="mt-4 font-serif text-lg text-ink">{{ $place->name }}</h3>
                            <p class="mt-1 flex-1 text-sm text-ink-light">{{ $place->description }}</p>
                            @if ($place->convention_description)
                                <p class="mt-3 flex items-start gap-2 rounded-lg bg-clay-50 p-3 text-sm font-medium text-clay-700">
                                    <x-icon name="gift" class="mt-0.5 h-4 w-4 shrink-0"/>
                                    <span>{{ $place->convention_description }}</span>
                                </p>
                            @endif
                            @if ($place->distance_walking)<p class="mt-3 inline-flex items-center gap-1.5 text-xs text-ink-soft"><x-icon name="pin" class="h-4 w-4"/> {{ $place->distance_walking }}</p>@endif
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-center text-ink-light">Presto aggiungeremo le attività convenzionate.</p>
            @endif
        </div>
    </section>
@endsection
