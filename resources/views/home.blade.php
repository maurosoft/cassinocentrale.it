@extends('layouts.app')

@section('content')
    {{-- ===================== HERO ===================== --}}
    <section class="relative isolate overflow-hidden">
        <img src="{{ asset('images/placeholders/hero.svg') }}" alt=""
             class="absolute inset-0 -z-10 h-full w-full object-cover" aria-hidden="true">
        <div class="absolute inset-0 -z-10 bg-ink/40"></div>

        <div class="container-bnb flex min-h-[70vh] flex-col items-center justify-center py-24 text-center text-cream-50">
            <span class="eyebrow text-cream-100">{{ $bnb['tagline'] }}</span>
            <h1 class="mt-4 max-w-3xl font-serif text-4xl font-semibold leading-tight sm:text-5xl md:text-6xl">
                {{ $settings['home.hero_title'] ?? $bnb['slogan'] }}
            </h1>
            <p class="mt-5 max-w-xl text-lg text-cream-100/90">
                {{ $settings['home.hero_subtitle'] ?? '' }}
            </p>
            <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('booking.create') }}" class="btn-primary">Prenota ora</a>
                <a href="{{ route('rooms.index') }}" class="btn-outline border-cream-100 text-cream-50 hover:bg-white/10">
                    Vedi le camere
                </a>
            </div>
        </div>
    </section>

    {{-- ===================== PERCHÉ SCEGLIERCI ===================== --}}
    <section class="py-20">
        <div class="container-bnb">
            <div class="mx-auto max-w-2xl text-center">
                <span class="eyebrow">Perché sceglierci</span>
                <h2 class="section-title">Nel cuore di Cassino, con tutto vicino</h2>
                <p class="mt-4 text-ink-light">{{ $settings['home.intro'] ?? '' }}</p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @php($features = [
                    ['t' => 'Posizione centrale', 'd' => 'A pochi passi da stazione FS, pullman, taxi, banche e Chiesa Madre.'],
                    ['t' => 'Wi-Fi in fibra', 'd' => 'Connessione veloce e gratuita in tutta la struttura.'],
                    ['t' => 'Colazione inclusa', 'd' => 'Ogni mattina una colazione genuina per iniziare bene la giornata.'],
                    ['t' => 'Camere eleganti', 'd' => 'Ambienti curati, silenziosi e climatizzati, con bagno privato.'],
                ])
                @foreach ($features as $f)
                    <div class="rounded-2xl border border-cream-300 bg-white p-6 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-clay-50 text-clay-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.75.75 0 0 1 1.04 0l7.5 7a.75.75 0 0 1 .23.55V20a1 1 0 0 1-1 1h-4v-6h-6v6H5a1 1 0 0 1-1-1v-8.95a.75.75 0 0 1 .23-.55z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-serif text-lg text-ink">{{ $f['t'] }}</h3>
                        <p class="mt-2 text-sm text-ink-light">{{ $f['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== ANTEPRIMA CAMERE ===================== --}}
    <section class="bg-cream-50 py-20">
        <div class="container-bnb">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <span class="eyebrow">Le nostre camere</span>
                    <h2 class="section-title">Quattro camere matrimoniali di charme</h2>
                </div>
                <a href="{{ route('rooms.index') }}" class="btn-ghost">Vedi tutte →</a>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($rooms as $room)
                    @include('partials.room-card', ['room' => $room])
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== OFFERTA (idea marketing) ===================== --}}
    @if ($settings['home.offer_enabled'] ?? false)
        <section class="py-16">
            <div class="container-bnb">
                <div class="flex flex-col items-center gap-6 rounded-3xl bg-clay-500 px-8 py-12 text-center text-cream-50 md:flex-row md:text-left">
                    <div class="flex-1">
                        <span class="eyebrow text-cream-100">Offerta</span>
                        <h2 class="mt-2 font-serif text-3xl font-semibold">{{ $settings['home.offer_title'] ?? 'Più notti, più risparmio' }}</h2>
                        <p class="mt-3 max-w-xl text-cream-100/90">{{ $settings['home.offer_text'] ?? '' }}</p>
                    </div>
                    <a href="{{ route('booking.create') }}" class="btn bg-cream-50 text-clay-700 hover:bg-white">
                        Approfitta ora
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== ANTEPRIMA SCOPRI CASSINO ===================== --}}
    <section class="bg-sage-50 py-20">
        <div class="container-bnb">
            <div class="mx-auto max-w-2xl text-center">
                <span class="eyebrow">Scopri Cassino</span>
                <h2 class="section-title">Storia, arte e natura a due passi</h2>
                <p class="mt-4 text-ink-light">{{ $settings['discover.intro'] ?? '' }}</p>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($places as $place)
                    <div class="card p-5">
                        <span class="badge">{{ $place->categoryLabel() }}</span>
                        <h3 class="mt-3 font-serif text-lg text-ink">{{ $place->name }}</h3>
                        <p class="mt-1 text-sm text-ink-light line-clamp-3">{{ \Illuminate\Support\Str::limit($place->description, 110) }}</p>
                        @if ($place->distance_walking)
                            <p class="mt-3 text-xs font-medium text-sage-700">🚶 {{ $place->distance_walking }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('discover.index') }}" class="btn-outline">Esplora Cassino</a>
            </div>
        </div>
    </section>

    {{-- ===================== RECENSIONI ===================== --}}
    @if (($settings['reviews.enabled'] ?? false) && $reviews->isNotEmpty())
        <section class="py-20">
            <div class="container-bnb">
                <div class="mx-auto max-w-2xl text-center">
                    <span class="eyebrow">Dicono di noi</span>
                    <h2 class="section-title">Le parole dei nostri ospiti</h2>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($reviews as $review)
                        <figure class="rounded-2xl border border-cream-300 bg-white p-6">
                            <div class="text-clay-500" aria-label="{{ $review->rating }} stelle su 5">
                                {{ str_repeat('★', $review->rating) }}<span class="text-cream-300">{{ str_repeat('★', 5 - $review->rating) }}</span>
                            </div>
                            @if ($review->title)
                                <figcaption class="mt-3 font-serif text-lg text-ink">{{ $review->title }}</figcaption>
                            @endif
                            <blockquote class="mt-2 text-sm text-ink-light">“{{ $review->comment }}”</blockquote>
                            <p class="mt-4 text-xs font-medium text-ink-soft">— {{ $review->guest_name }}@if ($review->source) · {{ $review->source }}@endif</p>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
