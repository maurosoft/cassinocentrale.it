<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Contracts\View\View;

class DiscoverController extends Controller
{
    /** Turismo: luoghi da visitare, raggruppati per categoria. */
    public function index(): View
    {
        $attractions = Place::active()->attractions()->ordered()->get()
            ->groupBy('category');

        return view('discover.index', compact('attractions'));
    }

    /** Attività: negozi, ristoranti, servizi (con convenzioni). */
    public function activities(): View
    {
        $conventions = Place::active()->conventions()->ordered()->get();

        return view('discover.activities', compact('conventions'));
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
