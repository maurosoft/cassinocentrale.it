@extends('layouts.app')

@section('title', ($room->name ?: 'Camera '.$room->number_name).' · '.$bnb['name'])
@section('meta_description', \Illuminate\Support\Str::limit($room->short_description ?: $room->description, 150))

@section('content')
    <div class="container-bnb py-8">
        <nav class="text-sm text-ink-soft">
            <a href="{{ route('home') }}" class="hover:text-clay-600">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('rooms.index') }}" class="hover:text-clay-600">Camere</a>
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $room->name ?: 'Camera '.$room->number_name }}</span>
        </nav>
    </div>

    <section class="container-bnb grid gap-10 pb-16 lg:grid-cols-[1.4fr_1fr]">
        {{-- Galleria (16:9, miniature cliccabili, apertura a schermo intero) --}}
        <div>
            @php($images = ! empty($room->images) ? $room->images : [$room->coverImage()])
            @php($imgUrl = fn ($i) => \Illuminate\Support\Str::startsWith($i, ['http', '/']) ? $i : asset($i))
            <button type="button" data-lightbox="room" data-src="{{ $imgUrl($images[0]) }}"
                    class="group block w-full overflow-hidden rounded-3xl bg-cream-200">
                <img src="{{ $imgUrl($images[0]) }}" alt="Camera {{ $room->number_name }}"
                     class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105">
            </button>
            @if (count($images) > 1)
                <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
                    @foreach (array_slice($images, 1) as $img)
                        <button type="button" data-lightbox="room" data-src="{{ $imgUrl($img) }}"
                                class="group overflow-hidden rounded-xl bg-cream-200">
                            <img src="{{ $imgUrl($img) }}" alt="Foto camera {{ $room->number_name }}"
                                 class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105">
                        </button>
                    @endforeach
                </div>
            @endif
            <p class="mt-2 text-center text-xs text-ink-soft">Clicca una foto per ingrandirla</p>
        </div>

        {{-- Dettagli --}}
        <div>
            <div class="flex items-center gap-2">
                <span class="badge-clay">Camera {{ $room->number_name }}</span>
                @if ($room->has_kitchenette)<span class="badge">Angolo cottura</span>@endif
            </div>
            <h1 class="mt-3 font-serif text-3xl font-semibold text-ink">{{ $room->name ?: 'Camera '.$room->number_name }}</h1>

            <p class="mt-4 text-ink-light">{{ $room->description }}</p>

            @if ($room->services->isNotEmpty())
                <h2 class="mt-8 font-serif text-xl text-ink">Servizi e comfort</h2>
                <ul class="mt-3 grid grid-cols-2 gap-2 text-sm text-ink-light">
                    @foreach ($room->services as $service)
                        <li class="flex items-center gap-2">
                            <span class="text-clay-500">✓</span>
                            <span>{{ $service->name }}@if ($service->pivot->extra_cost) <em class="text-ink-soft">(+€{{ number_format($service->pivot->extra_cost, 2, ',', '.') }})</em>@endif</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-8 rounded-2xl border border-cream-300 bg-cream-50 p-5 text-sm text-ink-light">
                <p><strong class="text-ink">Check-in:</strong> {{ $bnb['checkin']['from'] }}–{{ $bnb['checkin']['to'] }} · <strong class="text-ink">Check-out:</strong> entro le {{ $bnb['checkout']['until'] }}</p>
                @if ($room->rules)<p class="mt-2">{{ $room->rules }}</p>@endif
                <p class="mt-2">Ospiti max: {{ $room->max_guests }}</p>
            </div>

            <div class="mt-8 flex items-center justify-between rounded-2xl bg-clay-500 p-5 text-cream-50">
                <div>
                    <p class="text-xs uppercase tracking-wide text-cream-100">A partire da</p>
                    <p class="font-serif text-3xl font-semibold">€{{ number_format($room->base_price, 0, ',', '.') }}<span class="text-sm font-normal"> / notte</span></p>
                </div>
                <a href="{{ route('booking.create', ['room' => $room->slug]) }}" class="btn bg-cream-50 text-clay-700 hover:bg-white">
                    Prenota questa camera
                </a>
            </div>
        </div>
    </section>

    {{-- Altre camere --}}
    @if ($otherRooms->isNotEmpty())
        <section class="bg-cream-50 py-16">
            <div class="container-bnb">
                <h2 class="section-title text-center">Guarda anche le altre camere</h2>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($otherRooms as $room)
                        @include('partials.room-card', ['room' => $room])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
