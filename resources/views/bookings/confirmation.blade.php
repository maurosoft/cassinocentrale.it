@extends('layouts.app')

@section('title', 'Richiesta inviata · '.$bnb['name'])

@section('content')
    <section class="container-bnb py-16">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-sage-100 text-sage-700">
                <x-icon name="check" class="h-8 w-8"/>
            </div>
            <h1 class="mt-6 font-serif text-3xl font-semibold text-ink">Richiesta ricevuta, grazie!</h1>
            <p class="mt-3 text-ink-light">
                Abbiamo ricevuto la tua richiesta di prenotazione. Ti contatteremo al più presto per
                <strong class="text-ink">confermare la disponibilità</strong>. Il pagamento si effettua in struttura.
            </p>

            <div class="mx-auto mt-8 max-w-md rounded-2xl border border-cream-300 bg-white p-6 text-left text-sm">
                <div class="flex items-center justify-between border-b border-cream-200 pb-3">
                    <span class="text-ink-soft">Codice richiesta</span>
                    <span class="font-serif text-lg font-semibold text-clay-700">{{ $booking->reference }}</span>
                </div>
                <dl class="mt-3 space-y-2">
                    <div class="flex justify-between"><dt class="text-ink-soft">Ospite</dt><dd class="text-ink">{{ $booking->guest_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Camere</dt><dd class="text-ink">{{ $booking->rooms->map(fn ($r) => $r->room?->number_name)->filter()->join(', ') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Arrivo</dt><dd class="text-ink">{{ $booking->check_in->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Partenza</dt><dd class="text-ink">{{ $booking->check_out->format('d/m/Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink-soft">Notti</dt><dd class="text-ink">{{ $booking->nights() }}</dd></div>
                    <div class="flex justify-between border-t border-cream-200 pt-2"><dt class="text-ink-soft">Totale indicativo</dt><dd class="font-serif text-lg font-semibold text-clay-700">€{{ number_format($booking->total_price, 2, ',', '.') }}</dd></div>
                </dl>
            </div>

            <p class="mt-6 text-sm text-ink-soft">
                Per qualsiasi cosa: <a href="tel:{{ $bnb['contact']['phone_raw'] }}" class="text-clay-600">{{ $bnb['contact']['phone'] }}</a>
                · <a href="mailto:{{ $bnb['contact']['email'] }}" class="text-clay-600">{{ $bnb['contact']['email'] }}</a>
            </p>
            <a href="{{ route('home') }}" class="btn-outline mt-8">Torna alla home</a>
        </div>
    </section>
@endsection
