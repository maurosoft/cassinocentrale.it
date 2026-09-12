<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomClosure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClosureController extends Controller
{
    public function index(): View
    {
        $closures = RoomClosure::with('room')->orderBy('start_date')->get();
        $rooms = Room::ordered()->get();

        return view('admin.closures.index', compact('closures', 'rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'room_id' => ['nullable', 'exists:rooms,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:150'],
        ], [], [
            'start_date' => 'data inizio',
            'end_date' => 'data fine',
        ]);

        $data['room_id'] = $data['room_id'] ?: null;

        RoomClosure::create($data);

        return redirect()->route('admin.closures.index')->with('success', 'Chiusura aggiunta.');
    }

    public function destroy(RoomClosure $closure): RedirectResponse
    {
        $closure->delete();

        return redirect()->route('admin.closures.index')->with('success', 'Chiusura rimossa.');
    }
}
