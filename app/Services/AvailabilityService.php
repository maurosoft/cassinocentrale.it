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
    public function isRoomAvailable(Room $room, Carbon $checkIn, Carbon $checkOut, ?int $ignoreBookingId = null): bool
    {
        if ($this->hasOverlappingBooking($room->id, $checkIn, $checkOut, $ignoreBookingId)) {
            return false;
        }

        if ($this->isClosed($room->id, $checkIn, $checkOut)) {
            return false;
        }

        return true;
    }

    private function hasOverlappingBooking(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $ignoreBookingId = null): bool
    {
        return BookingRoom::where('room_id', $roomId)
            ->when($ignoreBookingId, fn ($q) => $q->where('booking_id', '!=', $ignoreBookingId))
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
     *
     * Carica i dati UNA volta sola e poi calcola in memoria (veloce).
     */
    public function fullyUnavailableDates(int $months = 8): array
    {
        $start = Carbon::today();
        $end = $start->copy()->addMonths($months);
        $roomIds = Room::active()->pluck('id');
        $totalRooms = $roomIds->count();

        if ($totalRooms === 0) {
            return [];
        }

        // Prenotazioni bloccanti nel periodo (una sola query)
        $bookingRooms = BookingRoom::whereHas('booking', function ($q) use ($start, $end) {
            $q->whereIn('status', self::BLOCKING_STATUSES)
                ->whereDate('check_in', '<', $end)
                ->whereDate('check_out', '>', $start);
        })->with('booking:id,check_in,check_out')->get();

        // Chiusure nel periodo (una sola query)
        $closures = RoomClosure::whereDate('start_date', '<', $end)
            ->whereDate('end_date', '>=', $start)
            ->get();

        $blocked = [];
        for ($day = $start->copy(); $day->lt($end); $day->addDay()) {
            $unavailable = [];

            foreach ($bookingRooms as $br) {
                $b = $br->booking;
                if ($b && $b->check_in->lte($day) && $b->check_out->gt($day)) {
                    $unavailable[$br->room_id] = true;
                }
            }

            foreach ($closures as $c) {
                if ($c->start_date->lte($day) && $c->end_date->gte($day)) {
                    if ($c->room_id === null) {
                        // Chiusura di tutto il B&B: tutte le camere non disponibili
                        $unavailable = array_fill_keys($roomIds->all(), true);
                        break;
                    }
                    $unavailable[$c->room_id] = true;
                }
            }

            if (count($unavailable) >= $totalRooms) {
                $blocked[] = $day->format('Y-m-d');
            }
        }

        return $blocked;
    }
}
