@php($nav = [
    ['label' => 'Home', 'route' => 'home'],
    ['label' => 'Camere', 'route' => 'rooms.index'],
    ['label' => 'Scopri Cassino', 'route' => 'discover.index'],
    ['label' => 'Contatti', 'route' => 'contact'],
])
<header class="sticky top-0 z-40 border-b border-cream-300 bg-cream-100/95 backdrop-blur">
    <nav class="container-bnb flex items-center justify-between py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="/icons/icon.svg" alt="" class="h-10 w-10" aria-hidden="true">
            <span class="leading-tight">
                <span class="block font-serif text-lg font-semibold text-clay-700">B&amp;B Cassino Centrale</span>
                <span class="block text-xs text-ink-soft">{{ $bnb['tagline'] }}</span>
            </span>
        </a>

        <div class="hidden items-center gap-8 md:flex">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="text-sm font-medium transition hover:text-clay-600 {{ request()->routeIs($item['route']) ? 'text-clay-700' : 'text-ink-light' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('booking.create') }}" class="btn-primary">Prenota</a>
        </div>

        <button type="button" data-menu-toggle
                class="inline-flex items-center rounded-lg p-2 text-ink md:hidden" aria-label="Apri il menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/>
            </svg>
        </button>
    </nav>

    <div data-menu class="hidden border-t border-cream-300 bg-cream-100 md:hidden">
        <div class="container-bnb flex flex-col gap-1 py-3">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($item['route']) ? 'bg-cream-200 text-clay-700' : 'text-ink-light' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ route('booking.create') }}" class="btn-primary mt-2">Prenota</a>
        </div>
    </div>
</header>
