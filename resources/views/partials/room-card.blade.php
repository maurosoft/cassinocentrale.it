@php($topServices = $room->services->take(4))
<article class="card card-lift flex flex-col" data-reveal>
    <a href="{{ route('rooms.show', $room) }}" class="block aspect-[4/3] overflow-hidden bg-cream-200">
        <img src="{{ \Illuminate\Support\Str::startsWith($room->coverImage(), ['http', '/']) ? $room->coverImage() : asset($room->coverImage()) }}"
             alt="Camera {{ $room->number_name }}"
             class="h-full w-full object-cover transition duration-500 hover:scale-105"
             loading="lazy">
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between">
            <span class="badge-clay">Camera {{ $room->number_name }}</span>
            @if ($room->has_kitchenette)
                <span class="badge">con angolo cottura</span>
            @endif
        </div>
        <h3 class="mt-3 font-serif text-xl text-ink">{{ $room->name ?: 'Camera '.$room->number_name }}</h3>
        <p class="mt-1 flex-1 text-sm text-ink-light">{{ $room->short_description }}</p>

        @if ($topServices->isNotEmpty())
            <ul class="mt-3 flex flex-wrap gap-2 text-xs text-ink-soft">
                @foreach ($topServices as $service)
                    <li class="rounded bg-cream-200 px-2 py-1">{{ $service->name }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-5 flex items-center justify-between">
            <p class="text-sm text-ink-soft">
                da <span class="font-serif text-2xl font-semibold text-clay-700">€{{ number_format($room->base_price, 0, ',', '.') }}</span>
                <span class="text-xs">/ notte</span>
            </p>
            <a href="{{ route('rooms.show', $room) }}" class="btn-outline !px-4 !py-2 text-xs">Vedi dettaglio</a>
        </div>
    </div>
</article>
