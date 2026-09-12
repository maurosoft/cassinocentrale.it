<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomClosure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        // Mese visualizzato (default: mese corrente)
        try {
            $month = $request->filled('month')
                ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
                : Carbon::today()->startOfMonth();
        } catch (\Throwable $e) {
            $month = Carbon::today()->startOfMonth();
        }

        $monthEnd = $month->copy()->endOfMonth();
        $rooms = Room::ordered()->get();

        // Prenotazioni che toccano il mese
        $bookings = Booking::with('rooms')
            ->whereIn('status', [Booking::STATUS_DRAFT, Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
            ->whereDate('check_in', '<=', $monthEnd)
            ->whereDate('check_out', '>', $month)
            ->get();

        // Chiusure che toccano il mese
        $closures = RoomClosure::whereDate('start_date', '<=', $monthEnd)
            ->whereDate('end_date', '>=', $month)
            ->get();

        // Costruisco la griglia: $grid[room_id][Y-m-d] = ['type'=>..., 'label'=>..., 'ref'=>...]
        $grid = [];
        $days = [];
        for ($d = $month->copy(); $d->lte($monthEnd); $d->addDay()) {
            $days[] = $d->copy();
        }

        foreach ($bookings as $booking) {
            foreach ($booking->rooms as $br) {
                // Notti occupate: da check_in a check_out - 1
                for ($d = $booking->check_in->copy(); $d->lt($booking->check_out); $d->addDay()) {
                    if ($d->lt($month) || $d->gt($monthEnd)) {
                        continue;
                    }
                    $grid[$br->room_id][$d->format('Y-m-d')] = [
                        'type' => $booking->status === Booking::STATUS_DRAFT ? 'request' : 'booked',
                        'label' => $booking->guest_name,
                        'ref' => $booking->id,
                    ];
                }
            }
        }

        foreach ($closures as $closure) {
            for ($d = $closure->start_date->copy(); $d->lte($closure->end_date); $d->addDay()) {
                if ($d->lt($month) || $d->gt($monthEnd)) {
                    continue;
                }
                $targetRooms = $closure->room_id ? [$closure->room_id] : $rooms->pluck('id')->all();
                foreach ($targetRooms as $rid) {
                    // Non sovrascrivo una prenotazione già presente
                    if (! isset($grid[$rid][$d->format('Y-m-d')])) {
                        $grid[$rid][$d->format('Y-m-d')] = [
                            'type' => 'closed',
                            'label' => $closure->reason ?: 'Chiuso',
                            'ref' => null,
                        ];
                    }
                }
            }
        }

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        return view('admin.calendar.index', compact('month', 'days', 'rooms', 'grid', 'prevMonth', 'nextMonth'));
    }
}
