<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with('rooms.room')->latest('check_in');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->query('q')) {
            $query->where(fn ($q) => $q
                ->where('guest_name', 'like', "%$search%")
                ->orWhere('guest_email', 'like', "%$search%")
                ->orWhere('reference', 'like', "%$search%"));
        }
        if ($roomId = $request->query('room')) {
            $query->whereHas('rooms', fn ($q) => $q->where('room_id', $roomId));
        }

        $bookings = $query->paginate(20)->withQueryString();
        $rooms = Room::ordered()->get();

        return view('admin.bookings.index', compact('bookings', 'rooms'));
    }

    public function show(Booking $booking): View
    {
        $booking->load('rooms.room');

        return view('admin.bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Booking::STATUSES))],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $booking->update($data);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Prenotazione aggiornata.');
    }
}
