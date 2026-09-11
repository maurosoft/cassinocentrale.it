@extends('layouts.app')

@section('title', 'Contatti e dove siamo · '.$bnb['name'])
@section('meta_description', 'Contatti del B&B Cassino Centrale: Viale Dante 6, Cassino (FR). Telefono, email e mappa. Nel pieno centro, vicino alla stazione.')

@section('content')
    <section class="py-14">
        <div class="container-bnb text-center">
            <span class="eyebrow">Contatti</span>
            <h1 class="section-title">Dove siamo</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink-light">
                Siamo nel pieno centro di Cassino, a pochi passi dalla stazione. Scrivici o chiamaci: saremo felici di aiutarti.
            </p>
        </div>
    </section>

    <section class="container-bnb grid gap-10 pb-20 lg:grid-cols-2">
        <div class="space-y-5">
            <div class="rounded-2xl border border-cream-300 bg-white p-6">
                <h2 class="font-serif text-xl text-ink">B&amp;B Cassino Centrale</h2>
                <ul class="mt-4 space-y-3 text-sm text-ink-light">
                    <li><strong class="text-ink">Indirizzo:</strong> {{ $bnb['contact']['address'] }}</li>
                    <li><strong class="text-ink">Telefono:</strong> <a href="tel:{{ $bnb['contact']['phone_raw'] }}" class="text-clay-600 hover:text-clay-700">{{ $bnb['contact']['phone'] }}</a></li>
                    <li><strong class="text-ink">Email:</strong> <a href="mailto:{{ $bnb['contact']['email'] }}" class="text-clay-600 hover:text-clay-700">{{ $bnb['contact']['email'] }}</a></li>
                    <li><strong class="text-ink">Check-in:</strong> {{ $bnb['checkin']['from'] }}–{{ $bnb['checkin']['to'] }}</li>
                    <li><strong class="text-ink">Check-out:</strong> entro le {{ $bnb['checkout']['until'] }}</li>
                </ul>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('booking.create') }}" class="btn-primary">Prenota</a>
                    <a href="{{ $bnb['contact']['maps_url'] }}" target="_blank" rel="noopener" class="btn-outline">Apri in Google Maps</a>
                </div>
            </div>

            <div class="rounded-2xl border border-cream-300 bg-cream-50 p-6">
                <h3 class="font-serif text-lg text-ink">Come arrivare</h3>
                <p class="mt-2 text-sm text-ink-light">
                    A pochi passi da Stazione FS, fermata pullman, taxi, banche e Chiesa Madre.
                    L'Abbazia di Montecassino si raggiunge in circa 20 minuti in auto.
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-cream-300">
            <iframe
                title="Mappa del B&B Cassino Centrale"
                src="https://maps.google.com/maps?q={{ urlencode('Viale Dante 6, Cassino FR') }}&z=15&output=embed"
                class="h-full min-h-[380px] w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
@endsection
