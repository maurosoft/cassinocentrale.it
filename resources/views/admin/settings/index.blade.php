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
