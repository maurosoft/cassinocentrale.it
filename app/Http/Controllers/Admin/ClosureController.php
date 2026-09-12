<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRoom;
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

        // Blocco la chiusura se nel periodo ci sono già prenotazioni (per la camera o per tutte).
        $conflicts = BookingRoom::query()
            ->when($data['room_id'], fn ($q) => $q->where('room_id', $data['room_id']))
            ->whereHas('booking', function ($q) use ($data) {
                $q->whereIn('status', [Booking::STATUS_DRAFT, Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                    ->whereDate('check_in', '<=', $data['end_date'])
                    ->whereDate('check_out', '>', $data['start_date']);
            })
            ->with('booking:id,guest_name,check_in,check_out', 'room:id,number_name')
            ->get();

        if ($conflicts->isNotEmpty()) {
            $list = $conflicts->map(fn ($br) => trim(
                ($br->room?->number_name ? 'Camera '.$br->room->number_name.' — ' : '').
                $br->booking?->guest_name.' ('.$br->booking?->check_in->format('d/m').'→'.$br->booking?->check_out->format('d/m').')'
            ))->unique()->take(5)->implode('; ');

            return back()->withInput()->with('error',
                'Non puoi chiudere questo periodo: ci sono già prenotazioni. '.$list.
                '. Prima sposta o annulla le prenotazioni interessate.');
        }

        RoomClosure::create($data);

        return redirect()->route('admin.closures.index')->with('success', 'Chiusura aggiunta.');
    }

    public function destroy(RoomClosure $closure): RedirectResponse
    {
        $closure->delete();

        return redirect()->route('admin.closures.index')->with('success', 'Chiusura rimossa.');
    }
}
