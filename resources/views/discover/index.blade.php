@extends('layouts.app')

@section('title', 'Scopri Cassino · '.$bnb['name'])
@section('meta_description', 'Cosa vedere a Cassino: Abbazia di Montecassino, luoghi della memoria della Seconda Guerra Mondiale, parco archeologico romano e attività convenzionate per gli ospiti del B&B.')

@section('content')
    <section class="bg-sage-50 py-14">
        <div class="container-bnb text-center">
            <span class="eyebrow">Scopri Cassino</span>
            <h1 class="section-title">Storia, arte e natura</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink-light">{{ $settings['discover.intro'] ?? '' }}</p>
        </div>
    </section>

    {{-- Luoghi turistici raggruppati per categoria --}}
    <section class="py-16">
        <div class="container-bnb space-y-14">
            @forelse ($attractions as $category => $places)
                <div>
                    <h2 class="mb-6 font-serif text-2xl text-ink">
                        {{ \App\Models\Place::CATEGORIES[$category] ?? \Illuminate\Support\Str::title($category) }}
                    </h2>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($places as $place)
                            <article class="card flex flex-col p-6">
                                <h3 class="font-serif text-xl text-ink">{{ $place->name }}</h3>
                                <p class="mt-2 flex-1 text-sm text-ink-light">{{ $place->description }}</p>
                                <div class="mt-4 space-y-1 text-xs text-ink-soft">
                                    @if ($place->distance_walking)<p>🚶 {{ $place->distance_walking }}</p>@endif
                                    @if ($place->distance_bus)<p>🚌 {{ $place->distance_bus }}</p>@endif
                                    @if ($place->distance_car)<p>🚗 {{ $place->distance_car }}</p>@endif
                                </div>
                                @if ($place->link)
                                    <a href="{{ $place->link }}" target="_blank" rel="noopener"
                                       class="mt-4 text-sm font-medium text-clay-600 hover:text-clay-700">Sito ufficiale →</a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-center text-ink-light">Presto aggiungeremo i luoghi da scoprire.</p>
            @endforelse
        </div>
    </section>

    {{-- Attività convenzionate --}}
    @if ($conventions->isNotEmpty())
        <section class="bg-cream-50 py-16">
            <div class="container-bnb">
                <div class="mx-auto max-w-2xl text-center">
                    <span class="eyebrow">Vantaggi per i nostri ospiti</span>
                    <h2 class="section-title">Attività convenzionate</h2>
                    <p class="mt-4 text-ink-light">Mostrando la conferma di prenotazione hai sconti e piccoli extra nelle attività amiche del B&amp;B.</p>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($conventions as $place)
                        <article class="rounded-2xl border border-clay-200 bg-white p-6">
                            <span class="badge-clay">Convenzione</span>
                            <h3 class="mt-3 font-serif text-lg text-ink">{{ $place->name }}</h3>
                            <p class="mt-1 text-sm text-ink-light">{{ $place->description }}</p>
                            @if ($place->convention_description)
                                <p class="mt-3 rounded-lg bg-clay-50 p-3 text-sm font-medium text-clay-700">🎁 {{ $place->convention_description }}</p>
                            @endif
                            @if ($place->distance_walking)<p class="mt-3 text-xs text-ink-soft">🚶 {{ $place->distance_walking }}</p>@endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
