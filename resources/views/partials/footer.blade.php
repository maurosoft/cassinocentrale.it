<footer class="mt-20 border-t border-cream-300 bg-sage-800 text-cream-100">
    <div class="container-bnb grid gap-10 py-14 md:grid-cols-3">
        <div>
            <span class="font-serif text-xl font-semibold text-cream-50">B&amp;B Cassino Centrale</span>
            <p class="mt-3 text-sm text-cream-200/80">{{ $bnb['slogan'] }}</p>
            <p class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-cream-200/75">
                <span>CIN: <strong class="font-medium text-cream-100">IT060019C1J7X8XZ56</strong></span>
                <span class="text-sage-600">·</span>
                <span>CIR: <strong class="font-medium text-cream-100">060019-B&amp;B-00029</strong></span>
                <button type="button" data-cin-open class="cin-i" aria-label="Cosa sono CIN e CIR?" title="Cosa sono?">i</button>
            </p>
        </div>

        <div class="text-sm">
            <h3 class="mb-3 font-serif text-lg text-cream-50">Contatti</h3>
            <p class="text-cream-200/90">{{ $bnb['contact']['address'] }}</p>
            <p class="mt-1">
                <a href="tel:{{ $bnb['contact']['phone_raw'] }}" class="hover:text-white">
                    Tel. {{ $bnb['contact']['phone'] }}
                </a>
            </p>
            <p class="mt-1">
                <a href="mailto:{{ $bnb['contact']['email'] }}" class="hover:text-white">
                    {{ $bnb['contact']['email'] }}
                </a>
            </p>
        </div>

        <div class="text-sm">
            <h3 class="mb-3 font-serif text-lg text-cream-50">Esplora</h3>
            <ul class="space-y-1.5">
                <li><a href="{{ route('rooms.index') }}" class="text-cream-200/90 hover:text-white">Le camere</a></li>
                <li><a href="{{ route('discover.index') }}" class="text-cream-200/90 hover:text-white">Turismo</a></li>
                <li><a href="{{ route('discover.activities') }}" class="text-cream-200/90 hover:text-white">Attività</a></li>
                <li><a href="{{ route('contact') }}" class="text-cream-200/90 hover:text-white">Dove siamo</a></li>
                <li><a href="{{ route('booking.create') }}" class="text-cream-200/90 hover:text-white">Prenota</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-sage-700">
        <div class="container-bnb flex flex-col items-center gap-3 py-5 text-xs text-cream-200/70">
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                <a href="{{ route('legal.privacy') }}" class="hover:text-white">Privacy Policy</a>
                <span class="text-sage-600">·</span>
                <a href="{{ route('legal.cookie') }}" class="hover:text-white">Cookie Policy</a>
                <span class="text-sage-600">·</span>
                <a href="{{ route('legal.terms') }}" class="hover:text-white">Termini e Condizioni</a>
            </div>
            <div class="flex flex-col items-center justify-between gap-2 sm:w-full sm:flex-row">
                <p>&copy; {{ date('Y') }} B&amp;B Cassino Centrale · Viale Dante 6, Cassino (FR)</p>
                <p class="flex items-center gap-1.5">
                    Realizzato con
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="#e11d48" aria-label="cuore" role="img">
                        <path d="M12 21s-6.7-4.35-9.33-8.02C.9 10.48 1.36 7.2 3.7 5.6a4.6 4.6 0 0 1 6 .77L12 8.9l2.3-2.53a4.6 4.6 0 0 1 6-.77c2.34 1.6 2.8 4.88 1.03 7.38C18.7 16.65 12 21 12 21z"/>
                    </svg>
                    da <a href="https://promoweb.me" target="_blank" rel="noopener" class="font-medium text-cream-100 underline decoration-dotted underline-offset-2 hover:text-white">PromoWeb</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Popup informativo CIN / CIR --}}
    <style>
        .cin-i{display:inline-flex;align-items:center;justify-content:center;width:17px;height:17px;border-radius:50%;
            border:1px solid rgba(250,244,234,.55);color:#faf4ea;font:700 10px/1 'Figtree',system-ui,sans-serif;
            cursor:pointer;background:transparent;transition:background .2s}
        .cin-i:hover{background:rgba(250,244,234,.18)}
        .cin-modal{position:fixed;inset:0;z-index:70;display:none;align-items:center;justify-content:center;padding:1rem;background:rgba(58,46,38,.65)}
        .cin-modal.open{display:flex}
        .cin-card{max-width:480px;width:100%;background:#faf4ea;color:#3a2e26;border-radius:16px;padding:1.5rem 1.6rem;
            box-shadow:0 24px 60px rgba(0,0,0,.4);position:relative}
        .cin-card h3{font:600 20px/1.2 'Cormorant Garamond',serif;color:#b85c38;margin:0 0 .2rem}
        .cin-card h4{font:600 14px/1.3 'Figtree',system-ui,sans-serif;margin:1rem 0 .15rem;color:#3a2e26}
        .cin-card p{font:400 13.5px/1.5 'Figtree',system-ui,sans-serif;color:#5b4f45;margin:.15rem 0}
        .cin-card code{background:#efe6d5;border-radius:6px;padding:1px 6px;font-size:12.5px;color:#3a2e26}
        .cin-x{position:absolute;top:.7rem;right:.9rem;background:transparent;border:0;font-size:22px;line-height:1;color:#9a8c7d;cursor:pointer}
        .cin-x:hover{color:#3a2e26}
    </style>

    <div class="cin-modal" data-cin-modal role="dialog" aria-modal="true" aria-labelledby="cin-title">
        <div class="cin-card">
            <button type="button" class="cin-x" data-cin-close aria-label="Chiudi">&times;</button>
            <h3 id="cin-title">CIN e CIR: cosa sono</h3>
            <p>Sono i codici identificativi obbligatori per legge per le strutture ricettive in Italia. Servono a rendere l'attività tracciabile e regolare.</p>
            <h4>CIN — Codice Identificativo Nazionale</h4>
            <p>Codice unico rilasciato dal <strong>Ministero del Turismo</strong> a livello nazionale. Identifica ufficialmente la struttura e va indicato negli annunci e nelle inserzioni.<br><code>IT060019C1J7X8XZ56</code></p>
            <h4>CIR — Codice Identificativo Regionale</h4>
            <p>Codice rilasciato dalla <strong>Regione Lazio</strong> per le strutture ricettive del territorio.<br><code>060019-B&amp;B-00029</code></p>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.querySelector('[data-cin-modal]');
            if (!modal) return;
            const open = () => modal.classList.add('open');
            const close = () => modal.classList.remove('open');
            document.querySelectorAll('[data-cin-open]').forEach(b => b.addEventListener('click', open));
            modal.querySelector('[data-cin-close]').addEventListener('click', close);
            modal.addEventListener('click', e => { if (e.target === modal) close(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
        })();
    </script>
</footer>
