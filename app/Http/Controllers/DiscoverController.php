<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Contracts\View\View;

class DiscoverController extends Controller
{
    public function index(): View
    {
        // Luoghi turistici, raggruppati per categoria.
        $attractions = Place::active()->attractions()->ordered()->get()
            ->groupBy('category');

        // Attività convenzionate (sconti/promo per gli ospiti).
        $conventions = Place::active()->conventions()->ordered()->get();

        return view('discover.index', compact('attractions', 'conventions'));
    }

    /** Scheda di dettaglio di un luogo/attività. */
    public function show(Place $place): View
    {
        abort_unless($place->is_active, 404);

        $related = Place::active()
            ->where('is_convention', $place->is_convention)
            ->whereKeyNot($place->getKey())
            ->ordered()
            ->take(3)
            ->get();

        return view('discover.show', compact('place', 'related'));
    }
}
