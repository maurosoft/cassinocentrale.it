<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
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

    /** Form di inserimento manuale prenotazione (admin/reception). */
    public function create(): View
    {
        $rooms = Room::active()->ordered()->get();
        $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.bookings.create', compact('rooms', 'customers'));
    }

    /** Salva una prenotazione inserita manualmente. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:4'],
            'room_id' => ['required', 'exists:rooms,id'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', 'in:'.implode(',', array_keys(Booking::STATUSES))],
            'notes' => ['nullable', 'string', 'max:1000'],
            'force' => ['nullable', 'boolean'],
        ], [
            'check_out.after' => 'La data di partenza deve essere successiva a quella di arrivo (controlla di non averle invertite).',
        ], [
            'check_in' => 'data di arrivo',
            'check_out' => 'data di partenza',
            'room_id' => 'camera',
            'first_name' => 'nome',
        ]);

        $room = Room::findOrFail($data['room_id']);
        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();

        // Controllo disponibilità (l'admin può forzare, es. prenotazione telefonica su accordo)
        if (! $request->boolean('force') && ! $this->availability->isRoomAvailable($room, $checkIn, $checkOut)) {
            return back()->withInput()->with('error', 'La camera non è libera per quelle date. Spunta "Forza comunque" se vuoi inserirla ugualmente.');
        }

        $nights = (int) $checkIn->diffInDays($checkOut);
        $quote = $this->pricing->quote($room, $nights, (int) $data['guests']);

        $customer = Customer::upsertFrom($data);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'reference' => 'CC-'.strtoupper(Str::random(6)),
            'guest_name' => $customer->fullName(),
            'guest_email' => $customer->email,
            'guest_phone' => $customer->phone,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_guests' => (int) $data['guests'],
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'],
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

        // NB: l'invio automatico delle notifiche al cliente arriverà nella Fase 4.

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Prenotazione creata.');
    }

    public function show(Booking $booking): View
    {
        $booking->load('rooms.room', 'customer');

        return view('admin.bookings.show', compact('booking'));
    }

    /** Aggiornamento rapido di stato/pagamento/note dalla scheda prenotazione. */
    public function statusUpdate(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Booking::STATUSES))],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $booking->update($data);

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Prenotazione aggiornata.');
    }

    /** Form di modifica completa della prenotazione. */
    public function edit(Booking $booking): View
    {
        $booking->load('rooms.room', 'customer');
        $rooms = Room::active()->ordered()->get();

        return view('admin.bookings.edit', compact('booking', 'rooms'));
    }

    /** Salva le modifiche complete (date, camera, cliente, ecc.). */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:4'],
            'room_id' => ['required', 'exists:rooms,id'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', 'in:'.implode(',', array_keys(Booking::STATUSES))],
            'payment_status' => ['required', 'in:unpaid,paid,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'internal_notes' => ['nullable', 'string'],
            'force' => ['nullable', 'boolean'],
        ], [
            'check_out.after' => 'La data di partenza deve essere successiva a quella di arrivo (controlla di non averle invertite).',
        ], [
            'check_in' => 'data di arrivo',
            'check_out' => 'data di partenza',
            'room_id' => 'camera',
            'first_name' => 'nome',
        ]);

        $room = Room::findOrFail($data['room_id']);
        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();

        // Disponibilità ignorando questa stessa prenotazione
        if (! $request->boolean('force') && ! $this->availability->isRoomAvailable($room, $checkIn, $checkOut, $booking->id)) {
            return back()->withInput()->with('error', 'La camera non è libera per quelle date. Spunta "Forza comunque" per salvare ugualmente.');
        }

        $nights = (int) $checkIn->diffInDays($checkOut);
        $quote = $this->pricing->quote($room, $nights, (int) $data['guests']);

        $customer = Customer::upsertFrom($data);

        $booking->update([
            'customer_id' => $customer->id,
            'guest_name' => $customer->fullName(),
            'guest_email' => $customer->email,
            'guest_phone' => $customer->phone,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_guests' => (int) $data['guests'],
            'notes' => $data['notes'] ?? null,
            'internal_notes' => $data['internal_notes'] ?? $booking->internal_notes,
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'total_price' => $quote['subtotal'],
            'discount_percent' => $quote['discount_percent'],
        ]);

        // Aggiorno la camera collegata
        $booking->rooms()->delete();
        $booking->rooms()->create([
            'room_id' => $room->id,
            'price_per_night' => $quote['price_per_night'],
            'nights' => $quote['nights'],
            'subtotal' => $quote['subtotal'],
        ]);

        // NB: la notifica automatica della modifica al cliente arriverà nella Fase 4.

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Prenotazione modificata.');
    }
}
