@extends('layouts.app')

@section('title', 'Prenota · '.$bnb['name'])
@section('meta_description', 'Verifica la disponibilità e prenota il tuo soggiorno al B&B Cassino Centrale.')

@section('content')
    <section class="bg-cream-50 py-12">
        <div class="container-bnb text-center">
            <span class="eyebrow">Prenota</span>
            <h1 class="section-title text-balance">Verifica disponibilità e prenota</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink-light">
                Scegli le date del soggiorno: ti mostriamo le camere libere e il prezzo, sconti inclusi.
                Prenotando direttamente con noi hai sempre la miglior tariffa.
            </p>
        </div>
    </section>

    <section class="container-bnb py-10">
        {{-- Messaggi --}}
        @if ($error)
            <div class="mx-auto mb-6 max-w-3xl rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ $error }}</div>
        @endif
        @if (session('error'))
            <div class="mx-auto mb-6 max-w-3xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        {{-- Ricerca disponibilità --}}
        <form method="GET" action="{{ route('booking.create') }}" class="mx-auto max-w-3xl rounded-2xl border border-cream-300 bg-white p-5 shadow-sm sm:p-6">
            <div class="grid gap-4 sm:grid-cols-[1fr_auto_auto] sm:items-end">
                <div>
                    <label class="block text-sm font-medium text-ink">Arrivo → Partenza</label>
                    <input type="text" data-date-range
                           data-disabled='@json($blockedDates)'
                           @if ($checkIn && $checkOut) data-start="{{ $checkIn->format('Y-m-d') }}" data-end="{{ $checkOut->format('Y-m-d') }}" @endif
                           placeholder="Scegli le date"
                           class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" readonly>
                    <input type="hidden" name="checkin" id="booking-checkin" value="{{ $checkIn?->format('Y-m-d') }}">
                    <input type="hidden" name="checkout" id="booking-checkout" value="{{ $checkOut?->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Ospiti</label>
                    <select name="guests" class="mt-1 rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @for ($g = 1; $g <= 4; $g++)
                            <option value="{{ $g }}" @selected($guests === $g)>{{ $g }} {{ $g === 1 ? 'ospite' : 'ospiti' }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn-primary sm:mb-0">Cerca</button>
            </div>
            <p class="mt-2 text-xs text-ink-soft">I giorni non selezionabili sono già occupati o chiusi.</p>
        </form>

        {{-- Risultati --}}
        @if ($checkIn && $checkOut && ! $error)
            <div class="mx-auto mt-8 max-w-5xl">
                <p class="text-sm text-ink-light">
                    <strong class="text-ink">{{ $checkIn->format('d/m/Y') }} → {{ $checkOut->format('d/m/Y') }}</strong>
                    · {{ $nights }} {{ $nights === 1 ? 'notte' : 'notti' }} · {{ $guests }} {{ $guests === 1 ? 'ospite' : 'ospiti' }}
                </p>

                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($roomsGrid as $entry)
                        @php($room = $entry['room'])
                        @php($q = $entry['quote'])
                        <article class="card flex flex-col">
                            <div class="relative aspect-[4/3] overflow-hidden bg-cream-200">
                                <img src="{{ \Illuminate\Support\Str::startsWith($room->coverImage(), ['http','/']) ? $room->coverImage() : asset($room->coverImage()) }}"
                                     alt="Camera {{ $room->number_name }}"
                                     class="h-full w-full object-cover {{ $entry['available'] ? '' : 'grayscale' }}" loading="lazy">
                                @unless ($entry['available'])
                                    <div class="absolute inset-0 bg-ink/25"></div>
                                    <div class="absolute left-[-25%] top-1/2 w-[150%] -translate-y-1/2 -rotate-[14deg] py-1.5 text-center text-xs font-semibold uppercase tracking-[0.3em] text-white shadow-lg {{ $entry['fits'] ? 'bg-red-600/90' : 'bg-ink/80' }}">
                                        {{ $entry['fits'] ? 'Occupata' : 'Non adatta' }}
                                    </div>
                                @endunless
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <span class="badge-clay w-fit">Camera {{ $room->number_name }}</span>
                                <h3 class="mt-2 font-serif text-lg text-ink">{{ $room->name ?: 'Camera '.$room->number_name }}</h3>
                                <p class="mt-1 flex-1 text-sm text-ink-light">{{ $room->short_description }}</p>

                                @if ($entry['available'] && $q)
                                    <div class="mt-4 rounded-xl bg-cream-50 p-3 text-sm">
                                        @if ($q['discount_percent'] > 0)
                                            <p class="text-ink-soft"><span class="line-through">€{{ number_format($q['base'], 2, ',', '.') }}</span>
                                            <span class="ml-1 rounded bg-sage-100 px-1.5 py-0.5 text-xs font-medium text-sage-700">-{{ (int) $q['discount_percent'] }}% {{ $q['discount_label'] }}</span></p>
                                        @endif
                                        <p class="font-serif text-2xl font-semibold text-clay-700">€{{ number_format($q['price_per_night'], 2, ',', '.') }}<span class="text-sm font-normal text-ink-soft"> / notte</span></p>
                                        <p class="mt-1 text-ink-light">Totale: <strong class="text-ink">€{{ number_format($q['subtotal'], 2, ',', '.') }}</strong></p>
                                    </div>
                                    <a href="{{ route('booking.create', ['checkin' => $checkIn->format('Y-m-d'), 'checkout' => $checkOut->format('Y-m-d'), 'guests' => $guests, 'room' => $room->slug]) }}#prenota"
                                       class="btn-primary mt-4 {{ $selectedRoom && $selectedRoom->id === $room->id ? 'ring-2 ring-clay-300 ring-offset-2' : '' }}">
                                        {{ $selectedRoom && $selectedRoom->id === $room->id ? 'Camera scelta ✓' : 'Prenota' }}
                                    </a>
                                @else
                                    <p class="mt-4 rounded-xl bg-cream-50 p-3 text-center text-sm text-ink-soft">
                                        {{ $entry['fits'] ? 'Non disponibile per queste date.' : 'Adatta fino a '.$room->max_guests.' ospiti.' }}
                                    </p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($roomsGrid->where('available', true)->isEmpty())
                    <div class="mt-6 rounded-2xl border border-cream-300 bg-white p-6 text-center">
                        <p class="font-serif text-lg text-ink">Nessuna camera disponibile per queste date</p>
                        <p class="mt-1 text-sm text-ink-light">Prova a cambiare le date, oppure contattaci.</p>
                        <a href="tel:{{ $bnb['contact']['phone_raw'] }}" class="btn-outline mt-3">Chiamaci: {{ $bnb['contact']['phone'] }}</a>
                    </div>
                @endif
            </div>
        @endif

        {{-- Form dati ospite (quando una camera è selezionata) --}}
        @if ($selectedRoom && $selectedQuote)
            <div id="prenota" class="mx-auto mt-12 max-w-3xl scroll-mt-24">
                <div class="rounded-2xl border border-clay-200 bg-white p-6 shadow-sm">
                    <h2 class="font-serif text-2xl text-ink">Completa la tua richiesta</h2>

                    {{-- Riepilogo --}}
                    <div class="mt-4 rounded-xl bg-cream-50 p-4 text-sm text-ink-light">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span><strong class="text-ink">{{ $selectedRoom->name ?: 'Camera '.$selectedRoom->number_name }}</strong> (Camera {{ $selectedRoom->number_name }})</span>
                            <span>{{ $checkIn->format('d/m/Y') }} → {{ $checkOut->format('d/m/Y') }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-cream-300 pt-2">
                            <span>{{ $selectedQuote['nights'] }} {{ $selectedQuote['nights'] === 1 ? 'notte' : 'notti' }} · {{ $guests }} {{ $guests === 1 ? 'ospite' : 'ospiti' }}@if ($selectedQuote['discount_percent'] > 0) · sconto {{ (int) $selectedQuote['discount_percent'] }}%@endif</span>
                            <span class="font-serif text-xl font-semibold text-clay-700">€{{ number_format($selectedQuote['subtotal'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            <ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('booking.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="check_in" value="{{ $checkIn->format('Y-m-d') }}">
                        <input type="hidden" name="check_out" value="{{ $checkOut->format('Y-m-d') }}">
                        <input type="hidden" name="guests" value="{{ $guests }}">
                        <input type="hidden" name="room" value="{{ $selectedRoom->slug }}">

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-ink">Nome *</label>
                                <input name="guest_first_name" value="{{ old('guest_first_name') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Cognome *</label>
                                <input name="guest_last_name" value="{{ old('guest_last_name') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Email *</label>
                                <input type="email" name="guest_email" value="{{ old('guest_email') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink">Telefono *</label>
                                <input name="guest_phone" value="{{ old('guest_phone') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink">Note (facoltative)</label>
                            <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Orario di arrivo, richieste particolari...">{{ old('notes') }}</textarea>
                        </div>
                        <label class="flex items-start gap-2 text-sm text-ink-light">
                            <input type="checkbox" name="privacy" value="1" class="mt-0.5 rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                            <span>Ho letto e accetto la <a href="{{ route('legal.privacy') }}" target="_blank" class="text-clay-600 underline">Privacy Policy</a>. *</span>
                        </label>

                        <div class="rounded-lg bg-sage-50 p-3 text-xs text-ink-light">
                            💡 La prenotazione è una <strong>richiesta</strong>: ti confermeremo noi la disponibilità. Il pagamento si effettua in struttura (i pagamenti online arriveranno a breve).
                        </div>

                        <button type="submit" class="btn-primary w-full">Invia richiesta di prenotazione</button>
                    </form>
                </div>
            </div>
        @endif
    </section>
@endsection
