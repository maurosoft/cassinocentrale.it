@extends('layouts.admin')

@section('title', 'WhatsApp')

@section('content')
    @php($waEnabled = (bool) \App\Support\Settings::get('whatsapp.enabled', false))

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Impostazioni globali --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border {{ $waEnabled ? 'border-sage-300 bg-sage-50' : 'border-cream-300 bg-white' }} p-5">
                <h2 class="font-serif text-lg text-ink">Stato invio WhatsApp</h2>
                <p class="mt-1 text-sm text-ink-light">Interruttore generale + numero dello staff. I singoli eventi (nuova/modifica/annullo) si attivano in <a href="{{ route('admin.settings.index') }}" class="text-clay-600 underline">Contenuti sito → Notifiche</a>.</p>
                <form method="POST" action="{{ route('admin.whatsapp.settings') }}" class="mt-4 space-y-4">
                    @csrf
                    <label class="flex items-center gap-2 text-sm text-ink">
                        <input type="checkbox" name="enabled" value="1" @checked($waEnabled) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                        Invio WhatsApp attivo
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-ink">Numero WhatsApp dello staff</label>
                        <input name="admin_whatsapp" value="{{ \App\Support\Settings::get('notify.admin_whatsapp') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="393331234567">
                        <p class="mt-1 text-xs text-ink-soft">Dove ricevere gli avvisi delle prenotazioni. Lascia vuoto per non riceverli.</p>
                    </div>
                    <button type="submit" class="btn-primary w-full">Salva stato</button>
                </form>
                <a href="{{ route('admin.logs.whatsapp') }}" class="mt-4 block text-center text-sm text-clay-600 hover:text-clay-700">Vedi il Log WhatsApp →</a>
            </div>
        </div>

        {{-- Provider --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="overflow-hidden rounded-2xl border border-cream-300 bg-white">
                <div class="border-b border-cream-300 px-5 py-3"><h2 class="font-serif text-lg text-ink">Provider configurati</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-cream-50 text-xs uppercase tracking-wide text-ink-soft">
                            <tr><th class="px-4 py-3">Nome</th><th class="px-4 py-3">Tipo</th><th class="px-4 py-3">Ordine</th><th class="px-4 py-3">Stato</th><th class="px-4 py-3 text-right">Azioni</th></tr>
                        </thead>
                        <tbody class="divide-y divide-cream-200">
                            @forelse ($providers as $p)
                                <tr class="hover:bg-cream-50">
                                    <td class="px-4 py-3 font-medium text-ink">{{ $p->name }}</td>
                                    <td class="px-4 py-3 text-ink-light">{{ $p->providerLabel() }}</td>
                                    <td class="px-4 py-3">{{ $p->order_fallback }}</td>
                                    <td class="px-4 py-3">@if ($p->is_active)<span class="rounded-full bg-sage-100 px-2.5 py-1 text-xs font-medium text-sage-700">Attivo</span>@else<span class="rounded-full bg-cream-200 px-2.5 py-1 text-xs text-ink-light">Spento</span>@endif</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.whatsapp.index', ['edit' => $p->id]) }}" class="rounded-lg p-2 text-ink-light hover:bg-cream-100"><x-icon name="edit" class="h-4 w-4"/></a>
                                            <form method="POST" action="{{ route('admin.whatsapp.destroy', $p) }}" onsubmit="return confirm('Rimuovere questo provider?')">@csrf @method('DELETE')<button class="rounded-lg p-2 text-red-500 hover:bg-red-50"><x-icon name="trash" class="h-4 w-4"/></button></form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-ink-soft">Nessun provider. Aggiungine uno qui sotto.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form nuovo/modifica provider --}}
            <div class="rounded-2xl border border-cream-300 bg-white p-5">
                <h2 class="font-serif text-lg text-ink">{{ $editing ? 'Modifica provider' : 'Aggiungi provider' }}</h2>
                @if ($errors->any())
                    <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"><ul class="list-inside list-disc">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ $editing ? route('admin.whatsapp.update', $editing) : route('admin.whatsapp.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                    @csrf
                    @if ($editing) @method('PUT') @endif
                    <div>
                        <label class="block text-sm font-medium text-ink">Nome *</label>
                        <input name="name" value="{{ old('name', $editing->name ?? '') }}" required class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="UoZap principale">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Tipo *</label>
                        <select name="provider" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                            @foreach (\App\Models\WhatsappProvider::PROVIDERS as $key => $label)
                                <option value="{{ $key }}" @selected(old('provider', $editing->provider ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-ink">Base URL (opzionale)</label>
                        <input name="base_url" value="{{ old('base_url', $editing->base_url ?? '') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="UoZap: https://uozap.it · Hooki: https://api.hooki.pro">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Token / API key {{ $editing ? '(vuoto = invariato)' : '*' }}</label>
                        <input name="token" type="password" {{ $editing ? '' : 'required' }} class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Instance / Gateway / Session ID</label>
                        <input name="instance_id" value="{{ old('instance_id', $editing->instance_id ?? '') }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Ordine fallback</label>
                        <input name="order_fallback" type="number" min="0" value="{{ old('order_fallback', $editing->order_fallback ?? 0) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink">Timeout (secondi)</label>
                        <input name="timeout" type="number" min="3" max="60" value="{{ old('timeout', $editing->timeout ?? 15) }}" class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-ink sm:col-span-2">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $editing->is_active ?? true)) class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                        Provider attivo (incluso nella catena di invio)
                    </label>
                    <div class="sm:col-span-2 flex items-center gap-3">
                        <button type="submit" class="btn-primary">{{ $editing ? 'Salva modifiche' : 'Aggiungi provider' }}</button>
                        @if ($editing)<a href="{{ route('admin.whatsapp.index') }}" class="btn-ghost">Annulla</a>@endif
                    </div>
                </form>
            </div>

            {{-- Test invio --}}
            @if ($providers->isNotEmpty())
                <div class="rounded-2xl border border-cream-300 bg-white p-5">
                    <h2 class="font-serif text-lg text-ink">Invia un messaggio di prova</h2>
                    <form method="POST" action="{{ route('admin.whatsapp.test') }}" class="mt-4 flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-ink">Provider</label>
                            <select name="provider_id" class="mt-1 rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500">
                                @foreach ($providers as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->providerLabel() }})</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink">Numero</label>
                            <input name="number" required class="mt-1 rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500" placeholder="393331234567">
                        </div>
                        <button type="submit" class="btn-outline !py-2.5">Invia test</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
