<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use App\Models\RoomClosure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Calcola la disponibilità delle camere per un periodo, tenendo conto
 * delle prenotazioni esistenti e dei periodi di chiusura.
 */
class AvailabilityService
{
    /** Stati di prenotazione che "occupano" una camera. */
    private const BLOCKING_STATUSES = [
        Booking::STATUS_DRAFT,
        Booking::STATUS_CONFIRMED,
        Booking::STATUS_COMPLETED,
    ];

    /** Camere disponibili per il periodo [checkIn, checkOut) e numero ospiti. */
    public function availableRooms(Carbon $checkIn, Carbon $checkOut, int $guests = 1): Collection
    {
        $rooms = Room::active()->ordered()
            ->where('max_guests', '>=', max($guests, 1))
            ->with('services')
            ->get();

        return $rooms->reject(
            fn (Room $room) => ! $this->isRoomAvailable($room, $checkIn, $checkOut)
        )->values();
    }

    /** Vero se la camera è libera per tutto il periodo richiesto. */
    public function isRoomAvailable(Room $room, Carbon $checkIn, Carbon $checkOut): bool
    {
        if ($this->hasOverlappingBooking($room->id, $checkIn, $checkOut)) {
            return false;
        }

        if ($this->isClosed($room->id, $checkIn, $checkOut)) {
            return false;
        }

        return true;
    }

    private function hasOverlappingBooking(int $roomId, Carbon $checkIn, Carbon $checkOut): bool
    {
        return BookingRoom::where('room_id', $roomId)
            ->whereHas('booking', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', self::BLOCKING_STATUSES)
                    ->whereDate('check_in', '<', $checkOut)
                    ->whereDate('check_out', '>', $checkIn);
            })
            ->exists();
    }

    /** Chiusure che coprono il periodo (per la camera o per tutto il B&B). */
    private function isClosed(int $roomId, Carbon $checkIn, Carbon $checkOut): bool
    {
        return RoomClosure::query()
            ->where(function ($q) use ($roomId) {
                $q->whereNull('room_id')->orWhere('room_id', $roomId);
            })
            // end_date è l'ultima notte chiusa (inclusa)
            ->whereDate('start_date', '<', $checkOut)
            ->whereDate('end_date', '>=', $checkIn)
            ->exists();
    }

    /**
     * Date completamente non disponibili nei prossimi $months mesi
     * (nessuna camera libera): servono a "spegnere" i giorni nel calendario.
     * Ritorna un array di stringhe 'Y-m-d'.
     */
    public function fullyUnavailableDates(int $months = 8): array
    {
        $start = Carbon::today();
        $end = $start->copy()->addMonths($months);
        $totalRooms = Room::active()->count();

        if ($totalRooms === 0) {
            return [];
        }

        $blocked = [];
        for ($day = $start->copy(); $day->lt($end); $day->addDay()) {
            $next = $day->copy()->addDay();
            // quante camere libere per la notte $day -> $next?
            $free = $this->availableRooms($day, $next, 1)->count();
            if ($free === 0) {
                $blocked[] = $day->format('Y-m-d');
            }
        }

        return $blocked;
    }
}
