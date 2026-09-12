<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sito in manutenzione · {{ config('bnb.name') }}</title>
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Figtree:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-cream-100">
    {{-- Sfondo: foto della struttura, sfumata --}}
    <div class="fixed inset-0 -z-10 bg-cover bg-center" style="background-image:url('/images/rooms/hero.jpg'); opacity:0.18;" aria-hidden="true"></div>
    <div class="fixed inset-0 -z-10 bg-gradient-to-b from-cream-100/70 via-cream-100/85 to-cream-100" aria-hidden="true"></div>

    <div class="relative flex min-h-screen items-center justify-center px-4 py-12 text-center">
        <div class="max-w-lg rounded-3xl bg-white/70 p-8 backdrop-blur-sm">
            <img src="/icons/icon.svg" alt="" class="mx-auto h-16 w-16">
            <h1 class="mt-6 font-serif text-3xl font-semibold text-clay-700">Torniamo subito</h1>
            <p class="mt-3 text-ink-light">
                {{ $message ?: 'Stiamo aggiornando il sito per offrirti un\'esperienza ancora migliore. Riprova tra poco: ti aspettiamo!' }}
            </p>
            <div class="mt-8 rounded-2xl border border-cream-300 bg-white p-5 text-sm text-ink-light">
                <p>Per informazioni o prenotazioni puoi contattarci:</p>
                <p class="mt-2">
                    <a href="tel:{{ config('bnb.contact.phone_raw') }}" class="font-medium text-clay-600 hover:text-clay-700">{{ config('bnb.contact.phone') }}</a>
                    <span class="mx-2 text-cream-400">·</span>
                    <a href="mailto:{{ config('bnb.contact.email') }}" class="font-medium text-clay-600 hover:text-clay-700">{{ config('bnb.contact.email') }}</a>
                </p>
            </div>
            <p class="mt-6 text-xs text-ink-soft">B&amp;B Cassino Centrale · Viale Dante 6, Cassino (FR)</p>
        </div>
    </div>
</body>
</html>
