<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $rooms = Room::active()->ordered()->with('services')->get();

        $places = Place::active()->attractions()->ordered()->take(4)->get();

        $reviews = Review::visible()->ordered()->take(3)->get();

        // Prezzo "a partire da" per la home.
        $priceFrom = $rooms->min('base_price');

        return view('home', compact('rooms', 'places', 'reviews', 'priceFrom'));
    }
}
