<footer class="mt-20 border-t border-cream-300 bg-sage-800 text-cream-100">
    <div class="container-bnb grid gap-10 py-14 md:grid-cols-3">
        <div>
            <span class="font-serif text-xl font-semibold text-cream-50">B&amp;B Cassino Centrale</span>
            <p class="mt-3 text-sm text-cream-200/80">{{ $bnb['slogan'] }}</p>
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
                <li><a href="{{ route('discover.index') }}" class="text-cream-200/90 hover:text-white">Scopri Cassino</a></li>
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
</footer>
