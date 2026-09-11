@props(['name' => 'star'])

@php
    // Icone a linea (stroke) coerenti, stile elegante. Aggiungerne qui se servono.
    $paths = [
        'pin' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>',
        'wifi' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z"/>',
        'coffee' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8.25h11.25V14a4.5 4.5 0 0 1-4.5 4.5H8.5A4.5 4.5 0 0 1 4 14V8.25Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.25 9.5h1.75a2.5 2.5 0 0 1 0 5h-1.75"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 3.5c.4.6.4 1.2 0 1.8M10.25 3.5c.4.6.4 1.2 0 1.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 21h11.25"/>',
        'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>',
        'snowflake' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M4.2 7.5l15.6 9M19.8 7.5 4.2 16.5M12 3l-2 2m2-2 2 2m-2 16-2-2m2 2 2-2M4.2 7.5l.1 2.8M4.2 7.5 7 7.4m12.8.1-2.8.1m2.8-.1-.1 2.8M4.2 16.5l2.8-.1m-2.8.1.1-2.8m15.5 2.8-.1-2.8m.1 2.8-2.8-.1"/>',
        'tv' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5v9.5H3.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 20.25h7.5M12 16.25v4"/>',
        'train' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75h10.5a2 2 0 0 1 2 2v8.5a2 2 0 0 1-2 2H6.75a2 2 0 0 1-2-2v-8.5a2 2 0 0 1 2-2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.75 10.25h14.5M8.5 20.25l-1.5 0m10 0-1.5 0M7.75 16.25 6 20.25m10.25-4L18 20.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 13.25h.008M15 13.25h.008"/>',
        'key' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.03 5.912l-2.72 2.72a2.25 2.25 0 0 1-1.59.658H8.25v1.5a.75.75 0 0 1-.75.75H6v1.5a.75.75 0 0 1-.75.75H3.75a.75.75 0 0 1-.75-.75v-1.94a1.5 1.5 0 0 1 .44-1.06l5.905-5.905A6 6 0 1 1 21.75 8.25Z"/>',
        'shop' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 9.5 5.2 5.4A2 2 0 0 1 7.12 4h9.76a2 2 0 0 1 1.92 1.4L20 9.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 9.5h16v2a2.5 2.5 0 0 1-4.2 1.84A2.5 2.5 0 0 1 12 13.34a2.5 2.5 0 0 1-3.8.0A2.5 2.5 0 0 1 4 11.5v-2Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 13.2V20h13v-6.8"/>',
        'utensils' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 3v7a2 2 0 0 0 4 0V3M8 3v18"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 3c-1.5 0-2.5 1.8-2.5 4.5S14.5 12 16 12v9"/>',
        'gift' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5h15v9a1 1 0 0 1-1 1h-13a1 1 0 0 1-1-1v-9Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 7.5h16.5v3H3.75zM12 7.5v13"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5S10.5 3.75 8.25 4.5 9.75 7.5 12 7.5Zm0 0s1.5-3.75 3.75-3S14.25 7.5 12 7.5Z"/>',
        'star' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.12 4.94 5.36.46c.5.04.7.66.32.99l-4.06 3.52 1.22 5.24c.11.48-.41.86-.84.6L12 16.98l-4.62 2.77c-.42.26-.95-.12-.83-.6l1.21-5.24-4.06-3.52c-.37-.33-.17-.95.33-.99l5.36-.46L11.48 3.5Z"/>',
        'phone' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.28 6.72 15 15 15h2.25a1.5 1.5 0 0 0 1.5-1.5v-2.6a1.5 1.5 0 0 0-1.14-1.45l-3.5-.87a1.5 1.5 0 0 0-1.5.5l-.77.94a12.05 12.05 0 0 1-5.06-5.06l.94-.77a1.5 1.5 0 0 0 .5-1.5l-.87-3.5A1.5 1.5 0 0 0 6.6 3.75H4A1.5 1.5 0 0 0 2.5 5.25v1.5Z"/>',
        'mail' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5v10.5H3.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 7.5 7.5 5.25L19.5 7.5"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'h-6 w-6']) }} fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
    {!! $paths[$name] ?? $paths['star'] !!}
</svg>
