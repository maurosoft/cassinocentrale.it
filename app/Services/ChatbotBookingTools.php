<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Strumenti che il chatbot "Zap" può usare durante il dialogo:
 *  - check(): legge il calendario vero e calcola prezzo/sconti reali;
 *  - book():  crea la prenotazione (stato "richiesta da approvare").
 *
 * Tutto passa dagli stessi servizi della pagina /prenota: niente dati inventati.
 */
class ChatbotBookingTools
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
        private readonly NotificationService $notifier,
    ) {}

    /**
     * Controlla la disponibilità e restituisce un testo che Zap userà per rispondere.
     */
    public function check(array $args): string
    {
        [$checkIn, $checkOut, $guests, $error] = $this->parse($args);
        if ($error) {
            return 'CONTROLLO NON RIUSCITO: '.$error;
        }

        $nights = (int) $checkIn->diffInDays($checkOut);
        $roomsNeeded = $this->pricing->roomsNeeded($guests);
        $freeRooms = $this->availability->availableRooms($checkIn, $checkOut, 1);

        $period = $checkIn->format('d/m/Y').' → '.$checkOut->format('d/m/Y')." ({$nights} notti, {$guests} ospiti, {$roomsNeeded} camera/e)";

        if ($freeRooms->count() >= $roomsNeeded) {
            $quote = $this->buildQuote($freeRooms->take($roomsNeeded), $nights, $guests, $roomsNeeded);
            $total = number_format($quote['total'], 2, ',', '.');
            $perNight = number_format($quote['total'] / max($nights, 1), 2, ',', '.');
            $labels = collect($quote['rooms'])->pluck('discount_label')->filter()->unique()->implode(', ');

            return "DISPONIBILE. {$period}. Totale {$total}€ (circa {$perNight}€ a notte)."
                .($labels ? " Sconti applicati: {$labels}." : '')
                .' Pagamento in struttura. Proponi all\'ospite di prenotare: raccogli nome, cognome, email, telefono e il consenso privacy, poi crea la prenotazione.';
        }

        // Non disponibile: proviamo a suggerire la prossima finestra libera.
        $next = $this->suggestNextWindow($checkIn, $nights, $roomsNeeded);
        $suggestion = $next
            ? ' Prima disponibilità utile per '.$nights.' notti: dal '.$next->format('d/m/Y').'. Proponila con gentilezza o chiedi altre date.'
            : ' Nessuna finestra libera trovata nei prossimi ~45 giorni: invita a contattare la struttura per verificare.';

        return "NON DISPONIBILE. {$period}: le camere risultano occupate.".$suggestion;
    }

    /**
     * Crea la prenotazione. Ricontrolla sempre disponibilità e prezzo (non si fida dei dati del modello).
     */
    public function book(array $args): string
    {
        [$checkIn, $checkOut, $guests, $error] = $this->parse($args);
        if ($error) {
            return 'PRENOTAZIONE NON CREATA: '.$error;
        }

        // Consenso privacy obbligatorio.
        if (empty($args['consent']) || $args['consent'] === 'false') {
            return 'PRENOTAZIONE NON CREATA: manca il consenso privacy. Chiedi all\'ospite di confermarlo prima di procedere.';
        }

        $firstName = trim((string) ($args['first_name'] ?? ''));
        $lastName = trim((string) ($args['last_name'] ?? ''));
        $email = trim((string) ($args['email'] ?? ''));
        $phone = trim((string) ($args['phone'] ?? ''));

        if ($firstName === '' || $lastName === '') {
            return 'PRENOTAZIONE NON CREATA: manca il nome o il cognome dell\'ospite. Chiedilo.';
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'PRENOTAZIONE NON CREATA: email mancante o non valida. Chiedila.';
        }
        if (strlen(preg_replace('/\D+/', '', $phone)) < 6) {
            return 'PRENOTAZIONE NON CREATA: numero di telefono mancante o non valido. Chiedilo.';
        }

        $nights = (int) $checkIn->diffInDays($checkOut);
        $roomsNeeded = $this->pricing->roomsNeeded($guests);
        $freeRooms = $this->availability->availableRooms($checkIn, $checkOut, 1)->take($roomsNeeded)->values();

        if ($freeRooms->count() < $roomsNeeded) {
            return 'PRENOTAZIONE NON CREATA: nel frattempo le date non sono più disponibili. Proponi altre date (usa di nuovo il controllo disponibilità).';
        }

        $quote = $this->buildQuote($freeRooms, $nights, $guests, $roomsNeeded);

        $customer = Customer::upsertFrom([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
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
            'notes' => trim((string) ($args['notes'] ?? '')) ?: null,
            'internal_notes' => 'Creata dal chatbot Zap.',
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

        // Notifiche (email/WhatsApp) rispettando i toggle admin.
        try {
            $this->notifier->bookingCreated($booking);
        } catch (\Throwable $e) {
            // Non blocchiamo la prenotazione se la notifica fallisce.
        }

        $total = number_format($quote['total'], 2, ',', '.');
        $rooms = collect($quote['rooms'])->map(fn ($r) => 'Camera '.$r['room']->number_name)->implode(', ');

        return "PRENOTAZIONE CREATA con successo. Codice: {$booking->reference}. "
            ."Periodo: {$checkIn->format('d/m/Y')} → {$checkOut->format('d/m/Y')}, {$guests} ospiti, {$rooms}. "
            ."Totale {$total}€, pagamento in struttura. Stato: RICHIESTA DA APPROVARE. "
            .'Comunica all\'ospite il codice prenotazione, ringrazialo e digli che confermeremo al più presto via email.';
    }

    /** Prossima data di inizio con abbastanza camere libere per lo stesso numero di notti. */
    private function suggestNextWindow(Carbon $from, int $nights, int $roomsNeeded, int $scanDays = 45): ?Carbon
    {
        for ($i = 1; $i <= $scanDays; $i++) {
            $start = $from->copy()->addDays($i);
            $end = $start->copy()->addDays($nights);
            if ($this->availability->availableRooms($start, $end, 1)->count() >= $roomsNeeded) {
                return $start;
            }
        }

        return null;
    }

    /** @param  \Illuminate\Support\Collection<int,Room>  $rooms */
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

    /**
     * Valida e normalizza gli argomenti comuni.
     *
     * @return array{0:?Carbon,1:?Carbon,2:int,3:?string}
     */
    private function parse(array $args): array
    {
        $checkIn = $this->toDate($args['checkin'] ?? null);
        $checkOut = $this->toDate($args['checkout'] ?? null);
        $guests = (int) ($args['guests'] ?? 0);

        if (! $checkIn || ! $checkOut) {
            return [null, null, 0, 'date non valide (servono nel formato AAAA-MM-GG).'];
        }
        if ($checkIn->lt(Carbon::today())) {
            return [null, null, 0, 'la data di arrivo è nel passato: chiedi una data futura.'];
        }
        if ($checkOut->lte($checkIn)) {
            return [null, null, 0, 'la partenza deve essere dopo l\'arrivo.'];
        }
        if ($checkIn->diffInDays($checkOut) > 30) {
            return [null, null, 0, 'soggiorno troppo lungo (oltre 30 notti): chiedi conferma o contatta la struttura.'];
        }
        if ($guests < 1 || $guests > 4) {
            return [null, null, 0, 'numero ospiti non valido: accettiamo da 1 a 4 persone.'];
        }

        return [$checkIn, $checkOut, $guests, null];
    }

    private function toDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }
        try {
            return Carbon::createFromFormat('Y-m-d', trim($value))->startOfDay();
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
