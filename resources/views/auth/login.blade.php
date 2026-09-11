<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accesso riservato · {{ config('bnb.name') }}</title>
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream-100">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <img src="/icons/icon.svg" alt="" class="h-12 w-12">
                    <span class="text-left leading-tight">
                        <span class="block font-serif text-xl font-semibold text-clay-700">B&amp;B Cassino Centrale</span>
                        <span class="block text-xs text-ink-soft">Area riservata</span>
                    </span>
                </a>
            </div>

            <div class="mt-8 rounded-2xl border border-cream-300 bg-white p-8 shadow-sm">
                <h1 class="font-serif text-2xl text-ink">Accedi</h1>
                <p class="mt-1 text-sm text-ink-light">Inserisci le tue credenziali per gestire il B&amp;B.</p>

                @if ($errors->any())
                    <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-ink">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500"
                               placeholder="nome@cassinocentrale.it">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-ink">Password</label>
                        <input id="password" name="password" type="password" required
                               class="mt-1 w-full rounded-lg border-cream-300 focus:border-clay-500 focus:ring-clay-500"
                               placeholder="••••••••">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-ink-light">
                        <input type="checkbox" name="remember" class="rounded border-cream-300 text-clay-500 focus:ring-clay-500">
                        Ricordami su questo dispositivo
                    </label>
                    <button type="submit" class="btn-primary w-full">Accedi</button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-ink-soft">
                <a href="{{ route('home') }}" class="hover:text-clay-600">← Torna al sito</a>
            </p>
        </div>
    </div>
</body>
</html>
