<div id="cookie-banner" hidden
     class="fixed inset-x-0 bottom-0 z-50 border-t border-cream-300 bg-white/95 backdrop-blur">
    <div class="container-bnb flex flex-col items-center gap-3 py-4 text-sm text-ink-light sm:flex-row sm:justify-between">
        <p class="max-w-2xl">
            Utilizziamo cookie tecnici necessari al funzionamento del sito e, in alcune pagine, servizi di terze parti
            (es. Google Maps). Continuando accetti l’uso dei cookie.
            <a href="{{ route('legal.cookie') }}" class="font-medium text-clay-600 underline hover:text-clay-700">Maggiori informazioni</a>.
        </p>
        <div class="flex shrink-0 gap-2">
            <button type="button" data-cookie-accept class="btn-primary !px-5 !py-2 text-xs">Accetta</button>
        </div>
    </div>
</div>

<script>
    (function () {
        try {
            var KEY = 'cc_cookie_consent';
            var banner = document.getElementById('cookie-banner');
            if (!banner) return;
            if (localStorage.getItem(KEY) !== 'accepted') {
                banner.hidden = false;
            }
            banner.querySelector('[data-cookie-accept]').addEventListener('click', function () {
                try { localStorage.setItem(KEY, 'accepted'); } catch (e) {}
                banner.hidden = true;
            });
        } catch (e) { /* se localStorage non è disponibile, non mostriamo il banner */ }
    })();
</script>
