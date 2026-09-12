@extends('layouts.app')

@section('title', 'Turismo a Cassino · '.$bnb['name'])
@section('meta_description', 'Cosa vedere a Cassino: Abbazia di Montecassino, luoghi della memoria della Seconda Guerra Mondiale, parco archeologico romano e altro ancora.')

@section('content')
    <section class="bg-sage-50 py-14">
        <div class="container-bnb text-center" data-reveal>
            <span class="eyebrow">Turismo</span>
            <h1 class="section-title text-balance">Cosa vedere a Cassino</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink-light">{{ $settings['discover.intro'] ?? '' }}</p>
            <a href="{{ route('discover.activities') }}" class="btn-outline mt-6">Vedi anche negozi e attività convenzionate →</a>
        </div>
    </section>

    {{-- Luoghi turistici raggruppati per categoria --}}
    <section class="py-16">
        <div class="container-bnb space-y-14">
            @forelse ($attractions as $category => $places)
                <div>
                    <h2 class="mb-6 font-serif text-2xl text-ink" data-reveal>
                        {{ \App\Models\Place::CATEGORIES[$category] ?? \Illuminate\Support\Str::title($category) }}
                    </h2>
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($places as $place)
                            <a href="{{ route('discover.show', $place) }}" class="card card-lift flex flex-col overflow-hidden" data-reveal>
                                <div class="aspect-[16/10] overflow-hidden bg-sage-100">
                                    @if ($place->image)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($place->image, ['http','/']) ? $place->image : asset($place->image) }}" alt="{{ $place->name }}" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy">
                                    @else
                                        <div class="flex h-full items-center justify-center text-sage-400"><x-icon name="pin" class="h-10 w-10"/></div>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <h3 class="font-serif text-xl text-ink">{{ $place->name }}</h3>
                                    <p class="mt-2 flex-1 text-sm text-ink-light">{{ \Illuminate\Support\Str::limit($place->description, 100) }}</p>
                                    <div class="mt-4 flex flex-wrap gap-x-3 gap-y-1 text-xs text-ink-soft">
                                        @if ($place->distance_walking)<span class="inline-flex items-center gap-1"><x-icon name="pin" class="h-3.5 w-3.5"/> {{ $place->distance_walking }}</span>@endif
                                        @if ($place->distance_bus)<span class="inline-flex items-center gap-1"><x-icon name="train" class="h-3.5 w-3.5"/> {{ $place->distance_bus }}</span>@endif
                                    </div>
                                    <span class="mt-4 text-sm font-medium text-clay-600">Scopri di più →</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-center text-ink-light">Presto aggiungeremo i luoghi da scoprire.</p>
            @endforelse
        </div>
    </section>
@endsection
