<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingStoreRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
    ) {}

    /** Pagina "Prenota": ricerca disponibilità e (se scelta) form ospite. */
    public function create(Request $request): View
    {
        [$checkIn, $checkOut] = $this->parseDates($request);
        $guests = (int) $request->integer('guests', 2);
        $guests = max(1, min($guests, 4));

        $availableRooms = collect();
        $quotes = [];
        $selectedRoom = null;
        $selectedQuote = null;
        $nights = 0;
        $error = null;

        if ($checkIn && $checkOut) {
            if ($checkOut <= $checkIn) {
                $error = 'La data di partenza deve essere successiva a quella di arrivo.';
            } else {
                $nights = (int) $checkIn->diffInDays($checkOut);
                $availableRooms = $this->availability->availableRooms($checkIn, $checkOut, $guests);

                foreach ($availableRooms as $room) {
                    $quotes[$room->id] = $this->pricing->quote($room, $nights, $guests);
                }

                // Camera preselezionata (da link "Prenota questa camera")
                if ($slug = $request->query('room')) {
                    $selectedRoom = $availableRooms->firstWhere('slug', $slug);
                    if ($selectedRoom) {
                        $selectedQuote = $quotes[$selectedRoom->id];
                    }
                }
            }
        }

        // Date non selezionabili nel calendario (giorni tutti pieni/chiusi)
        $blockedDates = $this->availability->fullyUnavailableDates();

        return view('bookings.create', compact(
            'checkIn', 'checkOut', 'guests', 'nights',
            'availableRooms', 'quotes', 'selectedRoom', 'selectedQuote',
            'blockedDates', 'error',
        ));
    }

    /** Registra la richiesta di prenotazione. */
    public function store(BookingStoreRequest $request): RedirectResponse
    {
        $checkIn = Carbon::parse($request->date('check_in'))->startOfDay();
        $checkOut = Carbon::parse($request->date('check_out'))->startOfDay();
        $guests = (int) $request->integer('guests');
        $room = Room::where('slug', $request->input('room'))->firstOrFail();

        // Ricontrollo la disponibilità lato server (sicurezza anti doppie prenotazioni)
        if (! $this->availability->isRoomAvailable($room, $checkIn, $checkOut)) {
            return back()
                ->withInput()
                ->with('error', 'Spiacenti, questa camera non è più disponibile per le date scelte. Prova con altre date.');
        }

        $nights = (int) $checkIn->diffInDays($checkOut);
        $quote = $this->pricing->quote($room, $nights, $guests);

        $booking = Booking::create([
            'reference' => $this->uniqueReference(),
            'guest_name' => trim($request->input('guest_first_name').' '.$request->input('guest_last_name')),
            'guest_email' => $request->input('guest_email'),
            'guest_phone' => $request->input('guest_phone'),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_guests' => $guests,
            'notes' => $request->input('notes'),
            'status' => Booking::STATUS_DRAFT, // richiesta da confermare
            'total_price' => $quote['subtotal'],
            'discount_percent' => $quote['discount_percent'],
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);

        $booking->rooms()->create([
            'room_id' => $room->id,
            'price_per_night' => $quote['price_per_night'],
            'nights' => $quote['nights'],
            'subtotal' => $quote['subtotal'],
        ]);

        // NB: l'invio automatico di email/WhatsApp arriverà in Fase 4.

        return redirect()->route('booking.confirmation', $booking->reference);
    }

    /** Pagina di conferma della richiesta. */
    public function confirmation(string $reference): View
    {
        $booking = Booking::with('rooms.room')->where('reference', $reference)->firstOrFail();

        return view('bookings.confirmation', compact('booking'));
    }

    /** Legge e valida le date dalla query string. */
    private function parseDates(Request $request): array
    {
        $checkIn = $this->toDate($request->query('checkin'));
        $checkOut = $this->toDate($request->query('checkout'));

        // Non permettere date passate per l'arrivo
        if ($checkIn && $checkIn->lt(Carbon::today())) {
            $checkIn = null;
            $checkOut = null;
        }

        return [$checkIn, $checkOut];
    }

    private function toDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function uniqueReference(): string
    {
        do {
            $ref = 'CC-'.strtoupper(Str::random(6));
        } while (Booking::where('reference', $ref)->exists());

        return $ref;
    }
}
