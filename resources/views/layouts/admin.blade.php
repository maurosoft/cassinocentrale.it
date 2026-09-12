<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Amministrazione') · {{ config('bnb.name') }}</title>
    <link rel="icon" href="/icons/icon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream-50 text-ink">
@php
    $user = auth()->user();
    // Voci del menu: etichetta, rotta, icona, ruoli ammessi.
    $menu = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'home', 'roles' => ['superadmin','reception','editor']],
        ['label' => 'Prenotazioni', 'route' => 'admin.bookings.index', 'active' => 'admin.bookings.*', 'icon' => 'calendar', 'roles' => ['superadmin','reception']],
        ['label' => 'Calendario', 'route' => 'admin.calendar.index', 'active' => 'admin.calendar.*', 'icon' => 'calendar', 'roles' => ['superadmin','reception']],
        ['label' => 'Chiusure', 'route' => 'admin.closures.index', 'active' => 'admin.closures.*', 'icon' => 'x', 'roles' => ['superadmin','reception']],
        ['label' => 'Clienti', 'route' => 'admin.customers.index', 'active' => 'admin.customers.*', 'icon' => 'users', 'roles' => ['superadmin','reception']],
        ['label' => 'WhatsApp', 'route' => 'admin.whatsapp.index', 'active' => 'admin.whatsapp.*', 'icon' => 'phone', 'roles' => ['superadmin']],
        ['label' => 'Log email', 'route' => 'admin.logs.email', 'active' => 'admin.logs.email', 'icon' => 'mail', 'roles' => ['superadmin','reception']],
        ['label' => 'Log WhatsApp', 'route' => 'admin.logs.whatsapp', 'active' => 'admin.logs.whatsapp', 'icon' => 'phone', 'roles' => ['superadmin','reception']],
        ['label' => 'Camere', 'route' => 'admin.rooms.index', 'active' => 'admin.rooms.*', 'icon' => 'bed', 'roles' => ['superadmin','reception','editor']],
        ['label' => 'Servizi', 'route' => 'admin.services.index', 'active' => 'admin.services.*', 'icon' => 'sparkles', 'roles' => ['superadmin','reception','editor']],
        ['label' => 'Luoghi & Negozi', 'route' => 'admin.places.index', 'active' => 'admin.places.*', 'icon' => 'pin', 'roles' => ['superadmin','editor']],
        ['label' => 'Recensioni', 'route' => 'admin.reviews.index', 'active' => 'admin.reviews.*', 'icon' => 'star', 'roles' => ['superadmin','editor']],
        ['label' => 'Contenuti sito', 'route' => 'admin.settings.index', 'active' => 'admin.settings.*', 'icon' => 'settings', 'roles' => ['superadmin','editor']],
        ['label' => 'Utenti', 'route' => 'admin.users.index', 'active' => 'admin.users.*', 'icon' => 'user', 'roles' => ['superadmin']],
    ];
    $canSee = fn ($item) => $user && ($user->isSuperadmin() || in_array($user->role, $item['roles'], true));
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside data-admin-sidebar class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col border-r border-cream-300 bg-white md:flex">
        <div class="flex items-center gap-3 border-b border-cream-300 px-5 py-4">
            <img src="/icons/icon.svg" alt="" class="h-9 w-9">
            <span class="leading-tight">
                <span class="block font-serif text-base font-semibold text-clay-700">Cassino Centrale</span>
                <span class="block text-xs text-ink-soft">Amministrazione</span>
            </span>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach ($menu as $item)
                @if ($canSee($item))
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs($item['active']) ? 'bg-clay-50 text-clay-700' : 'text-ink-light hover:bg-cream-100' }}">
                        <x-icon :name="$item['icon']" class="h-5 w-5"/>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
        <div class="border-t border-cream-300 p-3">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-ink-light hover:bg-cream-100">
                <x-icon name="external" class="h-5 w-5"/> Vedi il sito
            </a>
        </div>
    </aside>

    {{-- Contenuto --}}
    <div class="flex flex-1 flex-col md:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex items-center justify-between border-b border-cream-300 bg-cream-50/95 px-4 py-3 backdrop-blur">
            <button type="button" data-admin-menu-toggle class="rounded-lg p-2 text-ink md:hidden" aria-label="Menu">
                <x-icon name="menu" class="h-6 w-6"/>
            </button>
            <h1 class="font-serif text-lg text-ink">@yield('title', 'Amministrazione')</h1>
            <div class="flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-medium text-ink">{{ $user->name }}</p>
                    <p class="text-xs text-ink-soft">{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn-outline !px-3 !py-2 text-xs">Esci</button>
                </form>
            </div>
        </header>

        {{-- Menu mobile a scomparsa --}}
        <div data-admin-mobile class="hidden border-b border-cream-300 bg-white md:hidden">
            <nav class="space-y-1 p-3">
                @foreach ($menu as $item)
                    @if ($canSee($item))
                        <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-ink-light hover:bg-cream-100">
                            <x-icon :name="$item['icon']" class="h-5 w-5"/> {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div class="mb-5 rounded-lg border border-sage-200 bg-sage-50 px-4 py-3 text-sm text-sage-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.querySelector('[data-admin-menu-toggle]');
        const mobile = document.querySelector('[data-admin-mobile]');
        if (toggle && mobile) toggle.addEventListener('click', () => mobile.classList.toggle('hidden'));
    });
</script>
</body>
</html>
