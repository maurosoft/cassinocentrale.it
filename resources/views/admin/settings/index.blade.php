@extends('layouts.admin')

@section('title', 'Contenuti del sito')

@section('content')
    @php
        $keyLabels = [
            'seo.title' => 'Titolo del sito',
            'seo.description' => 'Descrizione (meta)',
            'seo.keywords' => 'Parole chiave',
            'home.hero_title' => 'Titolo principale (hero)',
            'home.hero_subtitle' => 'Sottotitolo (hero)',
            'home.intro' => 'Testo introduttivo',
            'home.offer_enabled' => 'Mostra la sezione offerta',
            'home.offer_title' => 'Titolo offerta',
            'home.offer_text' => 'Testo offerta',
            'discover.intro' => 'Introduzione «Scopri Cassino»',
            'reviews.enabled' => 'Mostra le recensioni in home',
            'whatsapp.button_enabled' => 'Pulsante WhatsApp (in disuso)',
        ];
        $isBool = fn ($s) => in_array($s->type, ['boolean','bool'], true);
        $isLong = fn ($s) => \Illuminate\Support\Str::contains($s->key, ['text','intro','description','subtitle']) || strlen((string)$s->value) > 80;
    @endphp

    @if (auth()->user()->isSuperadmin())
        @php($maintOn = (bool) \App\Support\Settings::get('site.maintenance_enabled', false))
        <div class="mb-6 rounded-2xl border {{ $maintOn ? 'border-amber-300 bg-amber-50' : 'border-cream-300 bg-white' }} p-5">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $maintOn ? 'bg-amber-200 text-amber-800' : 'bg-cream-200 text-ink-light' }}"><x-icon name="settings" class="h-5 w-5"/></span>
                <div class="flex-1">
                    <h2 class="font-serif text-lg text-ink">Modalità manutenzione</h2>
                    <p class="mt-1 text-sm text-ink-light">
                        Quando è attiva, i visitatori vedono una pagina "Torniamo subito". Tu e lo staff continuate a navigare il sito (con un banner di avviso).
                        Stato attuale:
                        <strong class="{{ $maintOn ? 'text-amber-700' : 'text-sage-700' }}">{{ $maintOn ? 'ATTIVA' : 'spenta' }}</strong>.
                    </p>
                    <form method="POST" action="{{ route('admin.settings.maintenance') }}" class="mt-4 space-y-3">
                        @csrf
                        <label class="flex items-center gap-2 text-sm text-ink">
                            <input type="checkbox" name="maintenance_enabled" value="1" @checked($maintOn) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                            Attiva la modalità manutenzione
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-ink">Messaggio per i visitatori (facoltativo)</label>
                            <input name="maintenance_message" value="{{ \App\Support\Settings::get('site.maintenance_message') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Torniamo online tra poche ore...">
                        </div>
                        <button type="submit" class="btn-primary !py-2.5">Salva stato manutenzione</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Logo del sito --}}
    @php($logo = \App\Support\Settings::get('branding.logo'))
    <div class="mb-6 rounded-2xl border border-cream-300 bg-white p-5">
        <h2 class="font-serif text-lg text-ink">Logo del sito</h2>
        <p class="mt-1 text-sm text-ink-light">Carica il logo del B&amp;B (PNG, JPG, WEBP o SVG). Comparirà nella testata del sito.</p>
        <div class="mt-4 flex flex-wrap items-center gap-5">
            <div class="flex h-16 w-16 items-center justify-center rounded-xl border border-cream-300 bg-cream-50">
                <img src="{{ $logo ? asset($logo) : '/icons/icon.svg' }}" alt="Logo" class="max-h-14 max-w-14 object-contain">
            </div>
            <form method="POST" action="{{ route('admin.settings.branding') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="logo" accept="image/*" required class="text-sm text-ink-light file:mr-3 file:rounded-lg file:border-0 file:bg-clay-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-clay-700">
                <button type="submit" class="btn-primary !py-2">Carica logo</button>
            </form>
        </div>

        {{-- Favicon --}}
        @php($favicon = \App\Support\Settings::get('branding.favicon'))
        <div class="mt-6 border-t border-cream-200 pt-5">
            <h3 class="font-serif text-base text-ink">Favicon</h3>
            <p class="mt-1 text-sm text-ink-light">La piccola icona che appare nella scheda del browser (meglio quadrata, PNG/SVG/ICO).</p>
            <div class="mt-3 flex flex-wrap items-center gap-5">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-cream-300 bg-cream-50">
                    <img src="{{ $favicon ? asset($favicon) : '/icons/icon.svg' }}" alt="Favicon" class="max-h-8 max-w-8 object-contain">
                </div>
                <form method="POST" action="{{ route('admin.settings.favicon') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <input type="file" name="favicon" accept="image/png,image/svg+xml,image/x-icon,image/jpeg,image/webp" required class="text-sm text-ink-light file:mr-3 file:rounded-lg file:border-0 file:bg-clay-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-clay-700">
                    <button type="submit" class="btn-primary !py-2">Carica favicon</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Immagini della home (sfondo hero + fascia parallax) --}}
    @php($heroImg = \App\Support\Settings::get('branding.hero'))
    @php($parallaxImg = \App\Support\Settings::get('branding.parallax'))
    <div class="mb-6 rounded-2xl border border-cream-300 bg-white p-5">
        <h2 class="font-serif text-lg text-ink">Immagini della home</h2>
        <p class="mt-1 text-sm text-ink-light">
            La grande foto in cima (hero) e la fascia con effetto parallax più in basso.
            Consiglio: foto orizzontali di buona qualità (larghe almeno 1600px). Se non carichi nulla, restano quelle attuali.
        </p>
        <form method="POST" action="{{ route('admin.settings.hero') }}" enctype="multipart/form-data" class="mt-4 grid gap-5 sm:grid-cols-2">
            @csrf
            <div>
                <label class="block text-sm font-medium text-ink">Foto principale (hero)</label>
                <div class="mt-2 aspect-video w-full overflow-hidden rounded-xl border border-cream-300 bg-cream-50">
                    <img src="{{ $heroImg ? asset($heroImg) : asset('images/rooms/hero.jpg') }}" alt="Anteprima hero" class="h-full w-full object-cover">
                </div>
                <input type="file" name="hero" accept="image/*" class="mt-3 text-sm text-ink-light file:mr-3 file:rounded-lg file:border-0 file:bg-clay-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-clay-700">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink">Foto fascia (parallax)</label>
                <div class="mt-2 aspect-video w-full overflow-hidden rounded-xl border border-cream-300 bg-cream-50">
                    <img src="{{ $parallaxImg ? asset($parallaxImg) : asset('images/rooms/room-102.jpg') }}" alt="Anteprima parallax" class="h-full w-full object-cover">
                </div>
                <input type="file" name="parallax" accept="image/*" class="mt-3 text-sm text-ink-light file:mr-3 file:rounded-lg file:border-0 file:bg-clay-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-clay-700">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="btn-primary !py-2">Salva immagini</button>
            </div>
        </form>
    </div>

    {{-- Sconto seconda camera (gruppi) --}}
    @php($secondDiscount = \App\Support\Settings::get('pricing.second_room_discount_percent', config('bnb.pricing.second_room_discount_percent', 15)))
    <div class="mb-6 rounded-2xl border border-cream-300 bg-white p-5">
        <h2 class="font-serif text-lg text-ink">Sconto seconda camera (gruppi)</h2>
        <p class="mt-1 text-sm text-ink-light">Quando servono 2 camere (3-4 ospiti), la seconda camera riceve questo sconto.</p>
        <form method="POST" action="{{ route('admin.settings.pricing') }}" class="mt-4 flex flex-wrap items-center gap-3">
            @csrf
            <input type="number" name="second_room_discount_percent" min="0" max="90" value="{{ $secondDiscount }}" class="w-24 rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
            <span class="text-sm text-ink-light">% di sconto sulla seconda camera</span>
            <button type="submit" class="btn-primary !py-2">Salva</button>
        </form>
    </div>

    {{-- Notifiche (solo superadmin) --}}
    @if (auth()->user()->isSuperadmin())
        <div class="mb-6 rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Notifiche automatiche</h2>
            <p class="mt-1 text-sm text-ink-light">Scegli <strong>se</strong> e <strong>quando</strong> inviare le comunicazioni al cliente. L'invio vero e proprio (email/WhatsApp) si attiva con la Fase 4; qui imposti le preferenze.</p>
            <form method="POST" action="{{ route('admin.settings.notifications') }}" class="mt-4 space-y-4">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-ink-soft"><tr><th class="py-2">Evento</th><th class="py-2 text-center">Email</th><th class="py-2 text-center">WhatsApp</th></tr></thead>
                        <tbody class="divide-y divide-cream-200">
                            @php($n = fn ($k) => (bool) \App\Support\Settings::get('notify.'.$k, false))
                            <tr>
                                <td class="py-2">Nuova prenotazione</td>
                                <td class="py-2 text-center"><input type="checkbox" name="email_new" value="1" @checked($n('email_new')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"></td>
                                <td class="py-2 text-center"><input type="checkbox" name="whatsapp_new" value="1" @checked($n('whatsapp_new')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"></td>
                            </tr>
                            <tr>
                                <td class="py-2">Modifica prenotazione</td>
                                <td class="py-2 text-center"><input type="checkbox" name="email_change" value="1" @checked($n('email_change')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"></td>
                                <td class="py-2 text-center"><input type="checkbox" name="whatsapp_change" value="1" @checked($n('whatsapp_change')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"></td>
                            </tr>
                            <tr>
                                <td class="py-2">Annullamento prenotazione</td>
                                <td class="py-2 text-center"><input type="checkbox" name="email_cancel" value="1" @checked($n('email_cancel')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"></td>
                                <td class="py-2 text-center"><input type="checkbox" name="whatsapp_cancel" value="1" @checked($n('whatsapp_cancel')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-wrap items-center gap-3 rounded-lg bg-cream-50 p-3">
                    <label class="flex items-center gap-2 text-sm text-ink">
                        <input type="checkbox" name="reminder_enabled" value="1" @checked($n('reminder_enabled')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                        Promemoria prima dell'arrivo
                    </label>
                    <span class="text-sm text-ink-light">quando mancano</span>
                    <input type="number" name="reminder_days" min="0" max="30" value="{{ \App\Support\Settings::get('notify.reminder_days', 1) }}" class="w-20 rounded-lg border-cream-300 text-sm focus:border-clay-500 focus:ring-clay-500">
                    <span class="text-sm text-ink-light">giorni.</span>
                </div>
                <button type="submit" class="btn-primary !py-2.5">Salva notifiche</button>
            </form>
        </div>

        {{-- Email / SMTP --}}
        @php($smtp = fn ($k, $d = '') => \App\Support\Settings::get('smtp.'.$k, $d))
        <div class="mb-6 rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Email (SMTP)</h2>
            <p class="mt-1 text-sm text-ink-light">Dati del server di posta per inviare le email (conferme, ecc.). Se non li hai, chiedili al tuo provider di posta o hosting.</p>

            <form method="POST" action="{{ route('admin.settings.smtp') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-ink">Server (host)</label>
                    <input name="host" value="{{ $smtp('host') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="smtp.tuoprovider.it">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-ink">Porta</label>
                        <input name="port" type="number" value="{{ $smtp('port', 587) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Sicurezza</label>
                        <select name="encryption" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                            @foreach (['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'Nessuna'] as $val => $lbl)
                                <option value="{{ $val }}" @selected($smtp('encryption', 'tls') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Utente</label>
                    <input name="username" value="{{ $smtp('username') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="info@cassinocentrale.it">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Password</label>
                    <input name="password" type="password" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="•••••• (lascia vuoto per non cambiarla)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Mittente: email</label>
                    <input name="from_email" value="{{ $smtp('from_email') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="info@cassinocentrale.it">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Mittente: nome</label>
                    <input name="from_name" value="{{ $smtp('from_name', 'B&B Cassino Centrale') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-ink">Email dello staff (dove ricevere le notifiche)</label>
                    <input name="admin_email" value="{{ \App\Support\Settings::get('notify.admin_email', config('bnb.contact.email')) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <label class="flex items-center gap-2 text-sm text-ink sm:col-span-2">
                    <input type="checkbox" name="enabled" value="1" @checked($smtp('enabled')) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                    Attiva l'invio email tramite questo server
                </label>
                <div class="sm:col-span-2">
                    <button type="submit" class="btn-primary !py-2.5">Salva impostazioni email</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.settings.testEmail') }}" class="mt-4 flex flex-wrap items-end gap-3 border-t border-cream-200 pt-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-ink">Invia email di prova a</label>
                    <input name="test_email" type="email" required value="{{ config('bnb.contact.email') }}" class="mt-1 w-72 max-w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <button type="submit" class="btn-outline !py-2.5">Invia test</button>
            </form>
        </div>

        {{-- Pagamenti / Stripe --}}
        @php($stripeOn = (bool) \App\Support\Settings::get('stripe.enabled', false))
        <div class="mb-6 rounded-2xl border {{ $stripeOn ? 'border-sage-300 bg-sage-50' : 'border-cream-300 bg-white' }} p-5">
            <h2 class="font-serif text-lg text-ink">Pagamenti online (Stripe)</h2>
            <p class="mt-1 text-sm text-ink-light">Inserisci le chiavi di Stripe (le trovi nel tuo cruscotto Stripe → Sviluppatori → Chiavi API).</p>

            <form method="POST" action="{{ route('admin.settings.stripe') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-ink">Chiave segreta (Secret key) {{ \App\Support\Settings::get('stripe.secret_key') ? '(salvata — vuoto = invariata)' : '' }}</label>
                    <input name="secret_key" type="password" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="sk_live_… oppure sk_test_…">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Chiave pubblica (Publishable)</label>
                    <input name="public_key" value="{{ \App\Support\Settings::get('stripe.public_key') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="pk_live_… (opzionale)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Segreto webhook {{ \App\Support\Settings::get('stripe.webhook_secret') ? '(salvato — vuoto = invariato)' : '' }}</label>
                    <input name="webhook_secret" type="password" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="whsec_…">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Quota da incassare online</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input name="deposit_percent" type="number" min="1" max="100" value="{{ \App\Support\Settings::get('stripe.deposit_percent', 100) }}" class="w-24 rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        <span class="text-sm text-ink-light">% del totale (100 = intero, es. 30 = caparra)</span>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink sm:col-span-2">
                    <input type="checkbox" name="enabled" value="1" @checked($stripeOn) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                    Attiva i pagamenti online con Stripe
                </label>
                <div class="sm:col-span-2">
                    <button type="submit" class="btn-primary !py-2.5">Salva impostazioni Stripe</button>
                </div>
            </form>

            <div class="mt-4 rounded-lg bg-cream-50 p-3 text-xs text-ink-light">
                🔗 <strong>URL webhook</strong> da incollare in Stripe (Sviluppatori → Webhook → Aggiungi endpoint, evento <code>checkout.session.completed</code>):<br>
                <code class="mt-1 block break-all text-clay-700">{{ url('/stripe/webhook') }}</code>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')

        <div class="space-y-6">
            @foreach ($groups as $group => $settings)
                <div class="rounded-2xl border border-cream-300 bg-white p-5">
                    <h2 class="font-serif text-lg text-ink">{{ $labels[$group] ?? \Illuminate\Support\Str::title($group) }}</h2>
                    <div class="mt-4 space-y-4">
                        @foreach ($settings as $s)
                            <div>
                                @if ($isBool($s))
                                    <label class="flex items-center gap-2 text-sm text-ink">
                                        <input type="checkbox" name="settings[{{ $s->key }}]" value="1" @checked($s->typedValue()) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                                        {{ $keyLabels[$s->key] ?? $s->key }}
                                    </label>
                                @else
                                    <label class="block text-sm font-medium text-ink">{{ $keyLabels[$s->key] ?? $s->key }}</label>
                                    @if ($isLong($s))
                                        <textarea name="settings[{{ $s->key }}]" rows="3" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">{{ $s->value }}</textarea>
                                    @else
                                        <input name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button type="submit" class="btn-primary">Salva impostazioni</button>
        </div>
    </form>
@endsection
