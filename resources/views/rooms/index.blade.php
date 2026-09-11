@extends('layouts.app')

@section('title', 'Le camere · '.$bnb['name'])
@section('meta_description', 'Le 4 camere matrimoniali del B&B Cassino Centrale: bagno privato, aria condizionata, Wi-Fi in fibra e colazione inclusa, nel pieno centro di Cassino.')

@section('content')
    <section class="bg-cream-50 py-14">
        <div class="container-bnb text-center">
            <span class="eyebrow">Le nostre camere</span>
            <h1 class="section-title">Quattro camere, un'unica accoglienza</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink-light">
                Tutte le camere sono matrimoniali, con bagno privato, aria condizionata, TV, Wi-Fi in fibra e colazione inclusa.
                Scegli quella che preferisci: ti aspettiamo nel cuore di Cassino.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="container-bnb grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($rooms as $room)
                @include('partials.room-card', ['room' => $room])
            @empty
                <p class="col-span-full text-center text-ink-light">Al momento non ci sono camere disponibili.</p>
            @endforelse
        </div>
    </section>
@endsection
