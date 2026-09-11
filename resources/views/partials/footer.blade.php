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
        <div class="container-bnb flex flex-col items-center justify-between gap-2 py-5 text-xs text-cream-200/70 sm:flex-row">
            <p>&copy; {{ date('Y') }} B&amp;B Cassino Centrale. Tutti i diritti riservati.</p>
            <p>Struttura a conduzione femminile · Viale Dante 6, Cassino (FR)</p>
        </div>
    </div>
</footer>
