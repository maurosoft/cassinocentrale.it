@extends('layouts.app')

@section('content')
    {{-- ===================== HERO ===================== --}}
    <section class="relative isolate overflow-hidden">
        <img src="{{ asset('images/rooms/hero.jpg') }}" alt=""
             class="absolute inset-0 -z-10 h-full w-full object-cover" aria-hidden="true">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-ink/55 via-ink/45 to-ink/65"></div>

        <div class="container-bnb flex min-h-[74vh] flex-col items-center justify-center py-24 text-center text-cream-50">
            <span class="eyebrow animate-fade-up text-cream-100">{{ $bnb['tagline'] }}</span>
            <h1 class="mt-4 max-w-3xl animate-fade-up animate-delay-1 font-serif text-4xl font-semibold leading-tight text-balance sm:text-5xl md:text-6xl">
                {{ $settings['home.hero_title'] ?? $bnb['slogan'] }}
            </h1>
            <p class="mt-5 max-w-xl animate-fade-up animate-delay-2 text-lg text-cream-100/90">
                {{ $settings['home.hero_subtitle'] ?? '' }}
            </p>
            <div class="mt-9 flex animate-fade-up animate-delay-3 flex-wrap items-center justify-center gap-3">
                <a href="{{ route('booking.create') }}" class="btn-primary">Prenota ora</a>
                <a href="{{ route('rooms.index') }}" class="btn-outline border-cream-100 text-cream-50 hover:bg-white/10">
                    Vedi le camere
                </a>
            </div>

            <div class="mt-10 flex animate-fade-up animate-delay-3 flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-cream-100/90">
                <span class="inline-flex items-center gap-1.5"><x-icon name="star" class="h-4 w-4"/> 65 € a notte</span>
                <span class="inline-flex items-center gap-1.5"><x-icon name="coffee" class="h-4 w-4"/> Colazione inclusa</span>
                <span class="inline-flex items-center gap-1.5"><x-icon name="pin" class="h-4 w-4"/> In pieno centro</span>
            </div>
        </div>
    </section>

    {{-- ===================== PERCHÉ SCEGLIERCI ===================== --}}
    <section class="py-20">
        <div class="container-bnb">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <span class="eyebrow">Perché sceglierci</span>
                <h2 class="section-title text-balance">Nel cuore di Cassino, con tutto vicino</h2>
                <p class="mt-4 text-ink-light">{{ $settings['home.intro'] ?? '' }}</p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @php($features = [
                    ['i' => 'pin', 't' => 'Posizione centrale', 'd' => 'A pochi passi da stazione FS, pullman, taxi, banche e Chiesa Madre.'],
                    ['i' => 'wifi', 't' => 'Wi-Fi in fibra', 'd' => 'Connessione veloce e gratuita in tutta la struttura.'],
                    ['i' => 'coffee', 't' => 'Colazione inclusa', 'd' => 'Ogni mattina una colazione genuina per iniziare bene la giornata.'],
                    ['i' => 'sparkles', 't' => 'Camere eleganti', 'd' => 'Ambienti curati, silenziosi e climatizzati, con bagno privato.'],
                ])
                @foreach ($features as $f)
                    <div class="rounded-2xl border border-cream-300 bg-white p-6 text-center card-lift" data-reveal>
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-clay-50 text-clay-600">
                            <x-icon :name="$f['i']" class="h-7 w-7"/>
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
            <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
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
                <div class="relative flex flex-col items-center gap-6 overflow-hidden rounded-3xl bg-clay-500 px-8 py-12 text-center text-cream-50 md:flex-row md:text-left" data-reveal>
                    <div class="pointer-events-none absolute -right-10 -top-10 h-48 w-48 rounded-full bg-clay-400/40" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute -bottom-16 right-20 h-40 w-40 rounded-full bg-clay-600/40" aria-hidden="true"></div>
                    <div class="relative flex-1">
                        <span class="eyebrow text-cream-100">Offerta</span>
                        <h2 class="mt-2 font-serif text-3xl font-semibold">{{ $settings['home.offer_title'] ?? 'Offerta speciale' }}</h2>
                        <p class="mt-3 max-w-xl text-cream-100/90">{{ $settings['home.offer_text'] ?? '' }}</p>
                    </div>
                    <a href="{{ route('booking.create') }}" class="relative btn bg-cream-50 text-clay-700 hover:bg-white">
                        Approfitta ora
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== ANTEPRIMA SCOPRI CASSINO ===================== --}}
    <section class="bg-sage-50 py-20">
        <div class="container-bnb">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <span class="eyebrow">Scopri Cassino</span>
                <h2 class="section-title text-balance">Storia, arte e natura a due passi</h2>
                <p class="mt-4 text-ink-light">{{ $settings['discover.intro'] ?? '' }}</p>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($places as $place)
                    <div class="card card-lift p-5" data-reveal>
                        <span class="badge">{{ $place->categoryLabel() }}</span>
                        <h3 class="mt-3 font-serif text-lg text-ink">{{ $place->name }}</h3>
                        <p class="mt-1 text-sm text-ink-light">{{ \Illuminate\Support\Str::limit($place->description, 110) }}</p>
                        @if ($place->distance_walking)
                            <p class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-sage-700">
                                <x-icon name="pin" class="h-4 w-4"/> {{ $place->distance_walking }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center" data-reveal>
                <a href="{{ route('discover.index') }}" class="btn-outline">Esplora Cassino</a>
            </div>
        </div>
    </section>

    {{-- ===================== RECENSIONI ===================== --}}
    @if (($settings['reviews.enabled'] ?? false) && $reviews->isNotEmpty())
        <section class="py-20">
            <div class="container-bnb">
                <div class="mx-auto max-w-2xl text-center" data-reveal>
                    <span class="eyebrow">Dicono di noi</span>
                    <h2 class="section-title">Le parole dei nostri ospiti</h2>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($reviews as $review)
                        <figure class="rounded-2xl border border-cream-300 bg-white p-6 card-lift" data-reveal>
                            <div class="flex text-clay-500" aria-label="{{ $review->rating }} stelle su 5">
                                @for ($i = 0; $i < 5; $i++)
                                    <x-icon name="star" class="h-4 w-4 {{ $i < $review->rating ? 'fill-clay-500' : 'text-cream-300' }}"/>
                                @endfor
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
