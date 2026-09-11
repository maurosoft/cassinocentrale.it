@extends('layouts.app')

@section('title', 'Sei offline · '.$bnb['name'])

@section('content')
    <section class="container-bnb flex min-h-[60vh] flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl">📶</div>
        <h1 class="mt-4 font-serif text-3xl font-semibold text-ink">Sembra che tu sia offline</h1>
        <p class="mt-3 max-w-md text-ink-light">
            Non riusciamo a caricare questa pagina senza connessione.
            Controlla la rete e riprova: ti aspettiamo!
        </p>
        <button onclick="window.location.reload()" class="btn-primary mt-8">Riprova</button>
        <p class="mt-6 text-sm text-ink-soft">Oppure chiamaci: <a href="tel:{{ $bnb['contact']['phone_raw'] }}" class="text-clay-600">{{ $bnb['contact']['phone'] }}</a></p>
    </section>
@endsection
