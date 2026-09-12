@extends('layouts.app')

@section('title', $place->name.' · '.$bnb['name'])
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($place->description), 150))

@section('content')
    <div class="container-bnb py-6">
        <nav class="text-sm text-ink-soft">
            <a href="{{ route('home') }}" class="hover:text-clay-600">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('discover.index') }}" class="hover:text-clay-600">Scopri Cassino</a>
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $place->name }}</span>
        </nav>
    </div>

    <section class="container-bnb grid gap-8 pb-12 lg:grid-cols-[1.1fr_1fr]">
        {{-- Immagine --}}
        <div class="overflow-hidden rounded-3xl">
            @if ($place->image)
                <img src="{{ \Illuminate\Support\Str::startsWith($place->image, ['http','/']) ? $place->image : asset($place->image) }}"
                     alt="{{ $place->name }}" class="h-full max-h-[420px] w-full object-cover">
            @else
                <div class="flex h-64 items-center justify-center bg-sage-100 text-sage-500">
                    <x-icon name="pin" class="h-16 w-16"/>
                </div>
            @endif
        </div>

        {{-- Dati --}}
        <div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($place->is_convention)
                    <span class="badge-clay">Convenzione</span>
                    @if ($place->type)<span class="badge">{{ $place->typeLabel() }}</span>@endif
                @else
                    <span class="badge">{{ $place->categoryLabel() }}</span>
                @endif
            </div>

            <h1 class="mt-3 font-serif text-3xl font-semibold text-ink sm:text-4xl">{{ $place->name }}</h1>

            <div class="mt-6 space-y-3 rounded-2xl border border-cream-300 bg-cream-50 p-5 text-sm">
                @if ($place->distance_walking)
                    <p class="flex items-center gap-2 text-ink-light"><x-icon name="pin" class="h-4 w-4 text-clay-500"/> {{ $place->distance_walking }}</p>
                @endif
                @if ($place->distance_bus)
                    <p class="flex items-center gap-2 text-ink-light"><x-icon name="train" class="h-4 w-4 text-clay-500"/> {{ $place->distance_bus }}</p>
                @endif
                @if ($place->distance_car)
                    <p class="flex items-center gap-2 text-ink-light"><x-icon name="key" class="h-4 w-4 text-clay-500"/> {{ $place->distance_car }}</p>
                @endif
                @if ($place->convention_description)
                    <p class="flex items-start gap-2 rounded-lg bg-clay-50 p-3 font-medium text-clay-700"><x-icon name="gift" class="mt-0.5 h-4 w-4 shrink-0"/> <span>{{ $place->convention_description }}</span></p>
                @endif
            </div>

            <div class="mt-5 flex flex-wrap gap-3">
                @php($mapQuery = $place->lat && $place->lng ? $place->lat.','.$place->lng : $place->name.' Cassino')
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($mapQuery) }}" target="_blank" rel="noopener" class="btn-primary">
                    <x-icon name="pin" class="h-4 w-4"/> Apri nella mappa
                </a>
                @if ($place->link)
                    <a href="{{ $place->link }}" target="_blank" rel="noopener" class="btn-outline">
                        <x-icon name="external" class="h-4 w-4"/> Sito / approfondimento
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Descrizione --}}
    @if ($place->description)
        <section class="container-bnb pb-12">
            <div class="prose-bnb max-w-3xl">
                {!! nl2br(e($place->description)) !!}
            </div>
        </section>
    @endif

    {{-- Mappa --}}
    @if ($place->lat && $place->lng)
        <section class="container-bnb pb-12">
            <div class="overflow-hidden rounded-2xl border border-cream-300">
                <iframe title="Mappa di {{ $place->name }}" src="https://maps.google.com/maps?q={{ $place->lat }},{{ $place->lng }}&z=15&output=embed" class="h-80 w-full" loading="lazy"></iframe>
            </div>
        </section>
    @endif

    {{-- Correlati --}}
    @if ($related->isNotEmpty())
        <section class="bg-sage-50 py-14">
            <div class="container-bnb">
                <h2 class="section-title text-center">Altro da scoprire</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('discover.show', $r) }}" class="card card-lift block p-5">
                            <span class="badge">{{ $r->is_convention ? $r->typeLabel() : $r->categoryLabel() }}</span>
                            <h3 class="mt-3 font-serif text-lg text-ink">{{ $r->name }}</h3>
                            <p class="mt-1 text-sm text-ink-light">{{ \Illuminate\Support\Str::limit($r->description, 90) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
