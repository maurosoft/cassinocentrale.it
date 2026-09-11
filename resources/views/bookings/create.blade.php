@extends('layouts.app')

@section('title', 'Prenota · '.$bnb['name'])
@section('meta_description', 'Prenota il tuo soggiorno al B&B Cassino Centrale. Contattaci per disponibilità e migliori tariffe dirette.')

@section('content')
    @php($roomSlug = request()->query('room'))
    <section class="container-bnb py-16">
        <div class="mx-auto max-w-2xl text-center">
            <span class="eyebrow">Prenota</span>
            <h1 class="section-title">Prenota il tuo soggiorno</h1>
            <p class="mt-4 text-ink-light">
                La prenotazione online con calendario e pagamento arriverà a breve.
                Nel frattempo contattaci direttamente: rispondiamo veloci e con noi hai
                sempre la <strong class="text-ink">miglior tariffa</strong>, senza commissioni.
            </p>
            @if ($roomSlug)
                <p class="mt-3 text-sm text-clay-700">Camera scelta: <strong>{{ \Illuminate\Support\Str::upper(str_replace('-', ' ', $roomSlug)) }}</strong></p>
            @endif
        </div>

        <div class="mx-auto mt-10 grid max-w-3xl gap-4 sm:grid-cols-3">
            <a href="tel:{{ $bnb['contact']['phone_raw'] }}" class="rounded-2xl border border-cream-300 bg-white p-6 text-center transition hover:shadow-md">
                <div class="text-2xl">📞</div>
                <p class="mt-2 font-serif text-lg text-ink">Chiama</p>
                <p class="text-sm text-ink-light">{{ $bnb['contact']['phone'] }}</p>
            </a>

            @if (! empty($bnb['whatsapp_number']))
                <a href="https://wa.me/{{ $bnb['whatsapp_number'] }}?text={{ rawurlencode('Ciao! Vorrei prenotare al B&B Cassino Centrale'.($roomSlug ? ' (camera '.$roomSlug.')' : '').'.') }}"
                   target="_blank" rel="noopener" class="rounded-2xl border border-cream-300 bg-white p-6 text-center transition hover:shadow-md">
                    <div class="text-2xl">💬</div>
                    <p class="mt-2 font-serif text-lg text-ink">WhatsApp</p>
                    <p class="text-sm text-ink-light">Scrivici ora</p>
                </a>
            @endif

            <a href="mailto:{{ $bnb['contact']['email'] }}?subject={{ rawurlencode('Richiesta prenotazione') }}"
               class="rounded-2xl border border-cream-300 bg-white p-6 text-center transition hover:shadow-md">
                <div class="text-2xl">✉️</div>
                <p class="mt-2 font-serif text-lg text-ink">Email</p>
                <p class="text-sm text-ink-light">{{ $bnb['contact']['email'] }}</p>
            </a>
        </div>

        <div class="mx-auto mt-10 max-w-2xl rounded-2xl bg-sage-50 p-6 text-center text-sm text-ink-light">
            💡 Ricorda: fermandoti <strong class="text-ink">almeno 2 notti</strong> approfitti dello sconto sul soggiorno prenotando direttamente con noi.
        </div>
    </section>
@endsection
