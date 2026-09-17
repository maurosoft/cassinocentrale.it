@extends('layouts.admin')

@section('title', 'Chatbot Zap')

@section('content')
    <div class="mb-6 max-w-3xl">
        <p class="text-sm text-ink-light">
            Qui configuri <strong>Zap</strong>, l'assistente che risponde ai clienti sul sito.
            Scegli il servizio AI (provider), incolla la tua <strong>chiave API</strong>, premi
            <em>«Rileva modelli»</em> e scegli il modello. Poi spunta <em>«Attiva»</em> e salva.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.chatbot.save') }}" class="max-w-3xl space-y-6" id="zap-form">
        @csrf

        {{-- Attivazione + provider --}}
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <label class="flex items-center gap-2 text-sm font-medium text-ink">
                <input type="checkbox" name="enabled" value="1" @checked($current['enabled']) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                Attiva Zap sul sito (mostra la bollicina in basso a destra)
            </label>
            <label class="mt-3 flex items-start gap-2 text-sm text-ink">
                <input type="checkbox" name="booking_enabled" value="1" @checked($current['booking_enabled']) class="mt-0.5 rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                <span>
                    Consenti a Zap di <strong>controllare la disponibilità e prendere prenotazioni</strong> in chat.
                    <span class="block text-xs text-ink-soft">Legge il calendario vero e crea richieste "da approvare" (pagamento in struttura). Raccoglie anche il consenso privacy dell'ospite.</span>
                </span>
            </label>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Provider AI</label>
                    <select name="provider" id="provider-select" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                        @foreach ($providers as $key => $meta)
                            <option value="{{ $key }}" @selected($current['provider'] === $key)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-ink-soft" id="provider-help"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Chiave API <span id="key-status" class="font-normal"></span></label>
                    <input type="password" name="api_key" autocomplete="off" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Incolla qui la chiave (vuoto = non la cambio)">
                </div>
            </div>
        </div>

        {{-- Modello --}}
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Modello</h2>
            <p class="mt-1 text-sm text-ink-light">Premi «Rileva modelli» per farti riempire l'elenco dal provider, poi scegline uno.</p>
            <div class="mt-3 flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[220px]">
                    <label class="block text-sm font-medium text-ink">Modello scelto</label>
                    <input name="model" id="model-input" value="{{ $current['model'] }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="es. deepseek-chat">
                </div>
                <button type="button" id="detect-btn" class="btn-outline !py-2.5">Rileva modelli</button>
            </div>
            <div class="mt-3 hidden" id="model-select-wrap">
                <label class="block text-sm font-medium text-ink">Modelli disponibili</label>
                <select id="model-select" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500"></select>
            </div>
            <p class="mt-2 text-sm" id="detect-status"></p>
        </div>

        {{-- Personalità --}}
        <div class="rounded-2xl border border-cream-300 bg-white p-5">
            <h2 class="font-serif text-lg text-ink">Aspetto e messaggi</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Nome dell'assistente</label>
                    <input name="name" value="{{ $current['name'] }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink">Messaggio di benvenuto</label>
                    <input name="greeting" value="{{ $current['greeting'] }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-ink">Note aggiuntive (cose che Zap deve sapere)</label>
                <textarea name="notes" rows="4" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Es. Animali non ammessi. Parcheggio pubblico a 100m. Colazione servita 7:30-10:00...">{{ $current['notes'] }}</textarea>
                <p class="mt-1 text-xs text-ink-soft">Camere, prezzi e luoghi li conosce già in automatico: qui aggiungi solo dettagli extra.</p>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="btn-primary">Salva impostazioni</button>
        </div>
    </form>

    {{-- Prova rapida --}}
    <div class="mt-8 max-w-3xl rounded-2xl border border-cream-300 bg-white p-5">
        <h2 class="font-serif text-lg text-ink">Prova Zap</h2>
        <p class="mt-1 text-sm text-ink-light">Manda un messaggio di prova (funziona solo dopo aver salvato provider, modello e chiave, con Zap attivo).</p>
        <div class="mt-3 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <input id="test-input" class="w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="Es. Quanto costa una camera per 2 notti in due?">
            </div>
            <button type="button" id="test-btn" class="btn-outline !py-2.5">Invia prova</button>
        </div>
        <div id="test-reply" class="mt-3 hidden rounded-lg bg-cream-50 p-3 text-sm text-ink"></div>
    </div>

    {{-- Ultime conversazioni --}}
    <div class="mt-8 max-w-3xl rounded-2xl border border-cream-300 bg-white p-5">
        <h2 class="font-serif text-lg text-ink">Ultime conversazioni</h2>
        @if ($logs->isEmpty())
            <p class="mt-2 text-sm text-ink-light">Ancora nessuna conversazione.</p>
        @else
            <div class="mt-3 divide-y divide-cream-200">
                @foreach ($logs as $log)
                    <div class="py-3 text-sm">
                        <p class="text-ink"><span class="font-medium text-clay-700">Cliente:</span> {{ $log->question }}</p>
                        @if ($log->ok)
                            <p class="mt-1 text-ink-light"><span class="font-medium text-sage-700">Zap:</span> {{ \Illuminate\Support\Str::limit($log->answer, 240) }}</p>
                        @else
                            <p class="mt-1 text-red-600"><span class="font-medium">Errore:</span> {{ $log->error }}</p>
                        @endif
                        <p class="mt-1 text-xs text-ink-soft">{{ $log->created_at->format('d/m/Y H:i') }} · {{ $log->provider }} / {{ $log->model }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const providerSelect = document.getElementById('provider-select');
            const apiKeyInput = document.querySelector('input[name="api_key"]');
            const modelInput = document.getElementById('model-input');
            const modelSelect = document.getElementById('model-select');
            const modelWrap = document.getElementById('model-select-wrap');
            const detectBtn = document.getElementById('detect-btn');
            const detectStatus = document.getElementById('detect-status');
            const keyStatus = document.getElementById('key-status');
            const providerHelp = document.getElementById('provider-help');

            const PROVIDERS = @json($providers);
            const KEYS = @json($keyStatus);

            function refreshProviderInfo() {
                const p = providerSelect.value;
                const meta = PROVIDERS[p] || {};
                providerHelp.textContent = meta.help ? ('Chiave da: ' + meta.help) : '';
                keyStatus.textContent = KEYS[p] ? '✓ (salvata)' : '(non ancora salvata)';
                keyStatus.className = KEYS[p] ? 'font-normal text-sage-700' : 'font-normal text-ink-soft';
            }
            providerSelect.addEventListener('change', refreshProviderInfo);
            refreshProviderInfo();

            detectBtn.addEventListener('click', function () {
                detectStatus.textContent = 'Sto rilevando i modelli…';
                detectStatus.className = 'mt-2 text-sm text-ink-light';
                fetch('{{ route('admin.chatbot.detectModels') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ provider: providerSelect.value, api_key: apiKeyInput.value })
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.ok || !data.models || !data.models.length) {
                        detectStatus.textContent = '⚠️ ' + (data.error || 'Nessun modello trovato.');
                        detectStatus.className = 'mt-2 text-sm text-red-600';
                        return;
                    }
                    modelSelect.innerHTML = '';
                    data.models.forEach(function (m) {
                        const opt = document.createElement('option');
                        opt.value = m; opt.textContent = m;
                        if (m === modelInput.value) opt.selected = true;
                        modelSelect.appendChild(opt);
                    });
                    modelWrap.classList.remove('hidden');
                    if (!modelInput.value && data.models.length) modelInput.value = data.models[0];
                    detectStatus.textContent = '✓ ' + data.models.length + ' modelli rilevati. Scegline uno qui sotto.';
                    detectStatus.className = 'mt-2 text-sm text-sage-700';
                })
                .catch(() => {
                    detectStatus.textContent = '⚠️ Errore di rete durante il rilevamento.';
                    detectStatus.className = 'mt-2 text-sm text-red-600';
                });
            });

            modelSelect.addEventListener('change', function () {
                modelInput.value = modelSelect.value;
            });

            // Prova rapida
            const testBtn = document.getElementById('test-btn');
            const testInput = document.getElementById('test-input');
            const testReply = document.getElementById('test-reply');
            testBtn.addEventListener('click', function () {
                const msg = testInput.value.trim();
                if (!msg) return;
                testReply.classList.remove('hidden');
                testReply.textContent = 'Zap sta pensando…';
                fetch('{{ route('admin.chatbot.test') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ message: msg })
                })
                .then(r => r.json())
                .then(data => { testReply.textContent = data.reply || 'Nessuna risposta.'; })
                .catch(() => { testReply.textContent = 'Errore di rete.'; });
            });
        })();
    </script>
@endsection
