@php($waEnabled = ($settings['whatsapp.button_enabled'] ?? true) && ! empty($bnb['whatsapp_number']))
@if ($waEnabled)
    @php($waText = rawurlencode('Ciao! Vorrei informazioni sul B&B Cassino Centrale.'))
    <a href="https://wa.me/{{ $bnb['whatsapp_number'] }}?text={{ $waText }}"
       target="_blank" rel="noopener"
       class="fixed bottom-5 right-5 z-50 inline-flex items-center gap-2 rounded-full bg-[#25D366] px-4 py-3 text-sm font-medium text-white shadow-lg transition hover:brightness-95"
       aria-label="Chatta con noi su WhatsApp">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.75.46 3.45 1.32 4.95L2 22l5.3-1.38a9.86 9.86 0 0 0 4.73 1.2h.01c5.46 0 9.9-4.44 9.9-9.9 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.06c-.24.68-1.4 1.3-1.94 1.35-.5.05-.95.24-3.2-.67-2.7-1.06-4.42-3.8-4.55-3.98-.13-.18-1.09-1.45-1.09-2.77s.7-1.97.94-2.24c.24-.27.53-.34.7-.34.18 0 .35 0 .5.01.16.01.38-.06.6.46.24.55.8 1.9.87 2.04.07.13.11.29.02.47-.09.18-.13.29-.26.45-.13.16-.28.35-.4.47-.13.13-.27.28-.12.54.15.27.67 1.1 1.44 1.78.99.88 1.83 1.15 2.09 1.28.26.13.42.11.57-.07.15-.18.66-.77.83-1.03.18-.27.35-.22.6-.13.24.09 1.55.73 1.81.86.27.13.44.2.5.31.07.11.07.64-.17 1.32z"/>
        </svg>
        <span class="hidden sm:inline">Chatta con noi</span>
    </a>
@endif
