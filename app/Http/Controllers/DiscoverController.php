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
}
