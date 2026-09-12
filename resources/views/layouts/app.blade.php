<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $settings['seo.title'] ?? $bnb['name'])</title>
    <meta name="description" content="@yield('meta_description', $settings['seo.description'] ?? '')">
    <meta name="keywords" content="{{ $settings['seo.keywords'] ?? '' }}">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#b85c38">
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/icons/icon.svg">

    {{-- Open Graph (condivisione social) --}}
    <meta property="og:title" content="@yield('title', $settings['seo.title'] ?? $bnb['name'])">
    <meta property="og:description" content="@yield('meta_description', $settings['seo.description'] ?? '')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen flex flex-col">
    @if (($settings['site.maintenance_enabled'] ?? false) && auth()->check())
        <div class="bg-amber-500 px-4 py-2 text-center text-sm font-medium text-amber-950">
            🔧 Sito in <strong>manutenzione</strong>: i visitatori vedono la pagina di cortesia. Tu lo vedi perché sei nello staff.
            <a href="{{ route('admin.settings.index') }}" class="underline">Gestisci</a>
        </div>
    @endif

    @include('partials.nav')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.cookie-banner')

    {{-- Qui, in una fase successiva, andrà il widget del chatbot (in basso a destra). --}}

    @stack('scripts')
</body>
</html>
