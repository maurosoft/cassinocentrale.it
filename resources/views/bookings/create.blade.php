@extends('layouts.app')

@section('title', 'Prenota · '.$bnb['name'])
@section('meta_description', 'Verifica la disponibilità e prenota il tuo soggiorno al B&B Cassino Centrale.')

@section('content')
    <section class="bg-cream-50 py-12">
        <div class="container-bnb text-center">
            <span class="eyebrow">Prenota</span>
            <h1 class="section-title text-balance">Verifica disponibilità e prenota</h1>
            <p class="mx-auto mt-4 max-w-2xl text-ink-light">
                Scegli le date e il numero di ospiti: ti mostriamo le camere e i prezzi.
                Le nostre camere sono tutte matrimoniali, quindi per 3-4 persone proponiamo due camere.
            </p>
        </div>
    </section>

    <section class="container-bnb py-10">
        @if ($error)
            <div class="mx-auto mb-6 max-w-3xl rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ $error }}</div>
        @endif
        @if (session('error'))
            <div class="mx-auto mb-6 max-w-3xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        {{-- Ricerca --}}
        <form method="GET" action="{{ route('booking.create') }}" class="mx-auto max-w-3xl rounded-2xl border border-cream-300 bg-white p-5 shadow-sm sm:p-6">
            <div class="grid gap-4 sm:grid-cols-[1fr_auto_auto] sm:items-end">
                <div>
                    <label class="block text-sm font-medium text-ink">Arrivo → Partenza</label>
                    <input type="text" data-date-range data-disabled='@json($blockedDates)'
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
                <button type="submit" class="btn-primary">Cerca</button>
            </div>
            <p class="mt-2 text-xs text-ink-soft">I giorni non selezionabili sono già occupati o chiusi.</p>
        </form>

        {{-- Risultati --}}
        @if ($checkIn && $checkOut && ! $error)
            @php($selectedCount = $selectedRooms->count())
            <div id="camere" class="mx-auto mt-8 max-w-5xl scroll-mt-24">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-ink-light">
                        <strong class="text-ink">{{ $checkIn->format('d/m/Y') }} → {{ $checkOut->format('d/m/Y') }}</strong>
                        · {{ $nights }} {{ $nights === 1 ? 'notte' : 'notti' }} · {{ $guests }} {{ $guests === 1 ? 'ospite' : 'ospiti' }}
                    </p>
                    @if ($roomsNeeded > 1)
                        <span class="rounded-full bg-clay-50 px-3 py-1 text-xs font-medium text-clay-700">Servono {{ $roomsNeeded }} camere · scelte {{ $selectedCount }}/{{ $roomsNeeded }}</span>
                    @endif
                </div>

                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($roomsGrid as $entry)
                        @php($room = $entry['room'])
                        @php($isSelected = $entry['selected'])
                        @php($selectionFull = $selectedCount >= $roomsNeeded)
                        <article class="card flex flex-col {{ $isSelected ? 'ring-2 ring-clay-400' : '' }}">
                            <div class="relative aspect-[4/3] overflow-hidden bg-cream-200">
                                <img src="{{ \Illuminate\Support\Str::startsWith($room->coverImage(), ['http','/']) ? $room->coverImage() : asset($room->coverImage()) }}"
                                     alt="Camera {{ $room->number_name }}"
                                     class="h-full w-full object-cover {{ $entry['available'] ? '' : 'grayscale' }}" loading="lazy">
                                @unless ($entry['available'])
                                    <div class="absolute inset-0 bg-ink/25"></div>
                                    <div class="absolute left-[-25%] top-1/2 w-[150%] -translate-y-1/2 -rotate-[14deg] bg-red-600/90 py-1.5 text-center text-xs font-semibold uppercase tracking-[0.3em] text-white shadow-lg">Occupata</div>
                                @endunless
                                @if ($isSelected)
                                    <span class="absolute right-2 top-2 inline-flex items-center gap-1 rounded-full bg-clay-600 px-2 py-1 text-xs font-medium text-white"><x-icon name="check" class="h-3.5 w-3.5"/> Scelta</span>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <span class="badge-clay w-fit">Camera {{ $room->number_name }}</span>
                                <h3 class="mt-2 font-serif text-lg text-ink">{{ $room->name ?: 'Camera '.$room->number_name }}</h3>
                                <p class="mt-1 flex-1 text-sm text-ink-light">{{ $room->short_description }}</p>

                                @if ($entry['available'])
                                    <p class="mt-3 text-sm text-ink-soft">da <span class="font-serif text-xl font-semibold text-clay-700">€{{ number_format($room->base_price, 0, ',', '.') }}</span> / notte</p>
                                    @if ($isSelected)
                                        <a href="{{ route('booking.create', ['checkin' => $checkIn->format('Y-m-d'), 'checkout' => $checkOut->format('Y-m-d'), 'guests' => $guests, 'rooms' => $selectedSlugs->reject(fn ($s) => $s === $room->slug)->values()->all()]) }}#camere"
                                           class="btn-outline mt-3">Rimuovi</a>
                                    @elseif (! $selectionFull)
                                        <a href="{{ route('booking.create', ['checkin' => $checkIn->format('Y-m-d'), 'checkout' => $checkOut->format('Y-m-d'), 'guests' => $guests, 'rooms' => $selectedSlugs->merge([$room->slug])->unique()->values()->all()]) }}#camere"
                                           class="btn-primary mt-3">{{ $roomsNeeded > 1 ? 'Aggiungi' : 'Prenota' }}</a>
                                    @else
                                        <button type="button" disabled class="btn-ghost mt-3 cursor-not-allowed opacity-60">Hai già scelto {{ $roomsNeeded }} camere</button>
                                    @endif
                                @else
                                    <div class="mt-4 rounded-xl bg-cream-50 p-3 text-center text-sm text-ink-soft">
                                        @if ($entry['conflict'])
                                            <p class="font-medium text-ink">Occupata dal {{ $entry['conflict']['from']->format('d/m') }} al {{ $entry['conflict']['to']->format('d/m') }}</p>
                                            <p class="mt-1 text-xs">Prova altre date o un'altra camera.</p>
                                        @else
                                            Non disponibile per queste date.
                                        @endif
                                    </div>
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
                @elseif ($roomsNeeded > 1 && $selectedCount < $roomsNeeded)
                    <p class="mt-6 text-center text-sm text-ink-light">Seleziona <strong class="text-ink">{{ $roomsNeeded - $selectedCount }}</strong> {{ ($roomsNeeded - $selectedCount) === 1 ? 'altra camera' : 'camere' }} per proseguire.</p>
                @endif
            </div>
        @endif

        {{-- Form dati ospite (quando la selezione è completa) --}}
        @if ($quote)
            <div id="prenota" class="mx-auto mt-12 max-w-3xl scroll-mt-24">
                <div class="rounded-2xl border border-clay-200 bg-white p-6 shadow-sm">
                    <h2 class="font-serif text-2xl text-ink">Completa la tua richiesta</h2>

                    {{-- Riepilogo --}}
                    <div class="mt-4 space-y-2 rounded-xl bg-cream-50 p-4 text-sm text-ink-light">
                        <p class="text-xs">{{ $checkIn->format('d/m/Y') }} → {{ $checkOut->format('d/m/Y') }} · {{ $nights }} {{ $nights === 1 ? 'notte' : 'notti' }} · {{ $guests }} {{ $guests === 1 ? 'ospite' : 'ospiti' }}</p>
                        @foreach ($quote['rooms'] as $row)
                            <div class="flex items-center justify-between border-t border-cream-300 pt-2">
                                <span>
                                    <strong class="text-ink">{{ $row['room']->name ?: 'Camera '.$row['room']->number_name }}</strong>
                                    @if (($row['discount_percent'] ?? 0) > 0)<span class="ml-1 rounded bg-sage-100 px-1.5 py-0.5 text-xs font-medium text-sage-700">-{{ (int) $row['discount_percent'] }}% {{ $row['discount_label'] }}</span>@endif
                                </span>
                                <span class="font-medium text-ink">€{{ number_format($row['subtotal'], 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex items-center justify-between border-t border-cream-300 pt-2 text-base">
                            <span class="font-medium text-ink">Totale</span>
                            <span class="font-serif text-xl font-semibold text-clay-700">€{{ number_format($quote['total'], 2, ',', '.') }}</span>
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
                        @foreach ($selectedRooms as $r)
                            <input type="hidden" name="rooms[]" value="{{ $r->slug }}">
                        @endforeach

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
                            💡 La prenotazione è una <strong>richiesta</strong>: ti confermeremo noi la disponibilità. Il pagamento si effettua in struttura.
                        </div>

                        <button type="submit" class="btn-primary w-full">Invia richiesta di prenotazione</button>
                    </form>
                </div>
            </div>
        @endif
    </section>
@endsection
