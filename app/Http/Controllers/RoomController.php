<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Contracts\View\View;

class RoomController extends Controller
{
    /** Elenco di tutte le camere attive. */
    public function index(): View
    {
        $rooms = Room::active()->ordered()->with('services')->get();

        return view('rooms.index', compact('rooms'));
    }

    /** Dettaglio di una singola camera (raggiunta tramite lo slug). */
    public function show(Room $room): View
    {
        abort_unless($room->is_active, 404);

        $room->load('services');

        $otherRooms = Room::active()->ordered()
            ->whereKeyNot($room->getKey())
            ->take(3)
            ->get();

        return view('rooms.show', compact('room', 'otherRooms'));
    }
}
