<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $activeStatuses = [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED];

        // Camere occupate oggi (soggiorno in corso).
        $occupiedRoomIds = BookingRoom::query()
            ->whereHas('booking', function ($q) use ($today, $activeStatuses) {
                $q->whereIn('status', $activeStatuses)
                    ->whereDate('check_in', '<=', $today)
                    ->whereDate('check_out', '>', $today);
            })
            ->pluck('room_id')->unique();

        $totalRooms = Room::active()->count();
        $occupied = $occupiedRoomIds->count();

        $stats = [
            'arrivals_today' => Booking::whereIn('status', $activeStatuses)->whereDate('check_in', $today)->count(),
            'departures_today' => Booking::whereIn('status', $activeStatuses)->whereDate('check_out', $today)->count(),
            'occupied' => $occupied,
            'free' => max($totalRooms - $occupied, 0),
            'total_rooms' => $totalRooms,
            'occupancy' => $totalRooms > 0 ? round($occupied / $totalRooms * 100) : 0,
            'week' => Booking::whereIn('status', $activeStatuses)
                ->whereBetween('check_in', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])->count(),
            'month' => Booking::whereIn('status', $activeStatuses)
                ->whereBetween('check_in', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])->count(),
        ];

        $upcoming = Booking::with('rooms.room')
            ->whereIn('status', $activeStatuses)
            ->whereDate('check_in', '>=', $today)
            ->orderBy('check_in')
            ->take(6)
            ->get();

        $latest = Booking::with('rooms.room')->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'upcoming', 'latest'));
    }
}
