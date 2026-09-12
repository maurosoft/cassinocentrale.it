<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingStoreRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use App\Services\AvailabilityService;
use App\Services\NotificationService;
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
        private readonly NotificationService $notifier,
    ) {}

    /** Pagina "Prenota": ricerca disponibilità, scelta camere e form ospite. */
    public function create(Request $request): View
    {
        [$checkIn, $checkOut] = $this->parseDates($request);
        $guests = max(1, min((int) $request->integer('guests', 2), 4));
        $roomsNeeded = $this->pricing->roomsNeeded($guests);

        // Camere già scelte (slug) — supporta anche il vecchio parametro ?room=
        $selectedSlugs = collect($request->query('rooms', []))->filter()->unique()->values();
        if ($selectedSlugs->isEmpty() && $request->query('room')) {
            $selectedSlugs = collect([$request->query('room')]);
        }

        $roomsGrid = collect();
        $selectedRooms = collect();
        $quote = null;   // ['rooms' => [...], 'total' => float]
        $nights = 0;
        $error = null;

        if ($checkIn && $checkOut) {
            if ($checkOut <= $checkIn) {
                $error = 'La data di partenza deve essere successiva a quella di arrivo.';
            } else {
                $nights = (int) $checkIn->diffInDays($checkOut);

                $roomsGrid = Room::active()->ordered()->with('services')->get()->map(function (Room $room) use ($checkIn, $checkOut, $selectedSlugs) {
                    $available = $this->availability->isRoomAvailable($room, $checkIn, $checkOut);

                    return [
                        'room' => $room,
                        'available' => $available,
                        'selected' => $available && $selectedSlugs->contains($room->slug),
                        'conflict' => $available ? null : $this->availability->conflictRange($room, $checkIn, $checkOut),
                    ];
                });

                // Camere selezionate valide (disponibili), limitate al numero richiesto
                $selectedRooms = $roomsGrid->filter(fn ($e) => $e['selected'])
                    ->map(fn ($e) => $e['room'])
                    ->take($roomsNeeded)
                    ->values();

                if ($selectedRooms->count() === $roomsNeeded) {
                    $quote = $this->buildQuote($selectedRooms, $nights, $guests, $roomsNeeded);
                }
            }
        }

        $blockedDates = $this->availability->fullyUnavailableDates();

        $secondRoomDiscount = (int) \App\Support\Settings::get(
            'pricing.second_room_discount_percent',
            config('bnb.pricing.second_room_discount_percent', 15)
        );

        return view('bookings.create', compact(
            'checkIn', 'checkOut', 'guests', 'nights', 'roomsNeeded',
            'roomsGrid', 'selectedSlugs', 'selectedRooms', 'quote',
            'blockedDates', 'error', 'secondRoomDiscount',
        ));
    }

    /** Registra la richiesta di prenotazione (una o più camere). */
    public function store(BookingStoreRequest $request): RedirectResponse
    {
        $checkIn = Carbon::parse($request->date('check_in'))->startOfDay();
        $checkOut = Carbon::parse($request->date('check_out'))->startOfDay();
        $guests = (int) $request->integer('guests');
        $roomsNeeded = $this->pricing->roomsNeeded($guests);

        $rooms = Room::active()->whereIn('slug', (array) $request->input('rooms', []))->get();

        if ($rooms->count() !== $roomsNeeded) {
            return back()->withInput()->with('error', "Per {$guests} ospiti servono {$roomsNeeded} camere. Riprova la selezione.");
        }

        // Ricontrollo disponibilità di tutte le camere
        foreach ($rooms as $room) {
            if (! $this->availability->isRoomAvailable($room, $checkIn, $checkOut)) {
                return back()->withInput()->with('error', 'Una delle camere non è più disponibile per le date scelte. Riprova.');
            }
        }

        $nights = (int) $checkIn->diffInDays($checkOut);
        $quote = $this->buildQuote($rooms, $nights, $guests, $roomsNeeded);

        $customer = Customer::upsertFrom([
            'first_name' => $request->input('guest_first_name'),
            'last_name' => $request->input('guest_last_name'),
            'email' => $request->input('guest_email'),
            'phone' => $request->input('guest_phone'),
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'reference' => $this->uniqueReference(),
            'guest_name' => $customer->fullName(),
            'guest_email' => $customer->email,
            'guest_phone' => $customer->phone,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_guests' => $guests,
            'notes' => $request->input('notes'),
            'status' => Booking::STATUS_DRAFT,
            'total_price' => $quote['total'],
            'discount_percent' => 0,
            'payment_status' => Booking::PAYMENT_UNPAID,
        ]);

        foreach ($quote['rooms'] as $row) {
            $booking->rooms()->create([
                'room_id' => $row['room']->id,
                'price_per_night' => $row['price_per_night'],
                'nights' => $row['nights'],
                'subtotal' => $row['subtotal'],
            ]);
        }

        // Notifiche (email ora, WhatsApp in Fase 4b) — rispettano i toggle admin.
        $this->notifier->bookingCreated($booking);

        return redirect()->route('booking.confirmation', $booking->reference);
    }

    public function confirmation(string $reference): View
    {
        $booking = Booking::with('rooms.room')->where('reference', $reference)->firstOrFail();

        return view('bookings.confirmation', compact('booking'));
    }

    /** Costruisce il preventivo (1 camera con regole standard, 2+ camere con sconto gruppo). */
    private function buildQuote($rooms, int $nights, int $guests, int $roomsNeeded): array
    {
        if ($roomsNeeded === 1) {
            $room = $rooms->first();
            $q = $this->pricing->quote($room, $nights, $guests);

            return [
                'rooms' => [array_merge($q, ['room' => $room])],
                'total' => $q['subtotal'],
            ];
        }

        return $this->pricing->quoteGroup(collect($rooms), $nights);
    }

    private function parseDates(Request $request): array
    {
        $checkIn = $this->toDate($request->query('checkin'));
        $checkOut = $this->toDate($request->query('checkout'));

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
