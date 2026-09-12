@extends('layouts.app')

@section('title', 'Pagamento ricevuto · '.$bnb['name'])

@section('content')
    <section class="container-bnb py-16">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sage-100 text-sage-700">
                <x-icon name="check" class="h-8 w-8"/>
            </div>
            <h1 class="mt-6 font-serif text-3xl font-semibold text-ink">Grazie, pagamento ricevuto!</h1>
            <p class="mt-3 text-ink-light">
                Abbiamo registrato il tuo pagamento per la prenotazione <strong class="text-ink">{{ $booking->reference }}</strong>.
                Ti aspettiamo al B&amp;B Cassino Centrale! Riceverai a breve la conferma via email.
            </p>

            <div class="mx-auto mt-8 max-w-md rounded-2xl border border-cream-300 bg-white p-6 text-left text-sm">
                <div class="flex justify-between border-b border-cream-200 pb-3">
                    <span class="text-ink-soft">Prenotazione</span>
                    <span class="font-serif text-lg font-semibold text-clay-700">{{ $booking->reference }}</span>
                </div>
                <dl class="mt-3 space-y-2">
                    <div class="flex justify-between"><dt class="text-ink-soft">Ospite</dt><dd class="text-ink">{{ $booking->guest_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Arrivo</dt><dd class="text-ink">{{ $booking->check_in->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Partenza</dt><dd class="text-ink">{{ $booking->check_out->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Totale soggiorno</dt><dd class="font-medium text-ink">€{{ number_format($booking->total_price, 2, ',', '.') }}</dd></div>
                </dl>
            </div>

            <a href="{{ route('home') }}" class="btn-outline mt-8">Torna alla home</a>
        </div>
    </section>
@endsection
