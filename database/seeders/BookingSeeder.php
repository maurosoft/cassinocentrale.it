<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Room::orderBy('number_name')->get();
        if ($rooms->isEmpty()) {
            return;
        }

        // Prenotazioni finte con stati diversi, utili per testare calendario e dashboard.
        $samples = [
            ['guest' => 'Famiglia Rossi', 'email' => 'rossi@example.com', 'in' => '+3 days', 'nights' => 2, 'status' => Booking::STATUS_CONFIRMED, 'room' => '102'],
            ['guest' => 'Luca Bianchi', 'email' => 'luca@example.com', 'in' => '+10 days', 'nights' => 3, 'status' => Booking::STATUS_CONFIRMED, 'room' => '205'],
            ['guest' => 'Sara Verdi', 'email' => 'sara@example.com', 'in' => '-14 days', 'nights' => 1, 'status' => Booking::STATUS_COMPLETED, 'room' => '103'],
            ['guest' => 'Tom Müller', 'email' => 'tom@example.com', 'in' => '+20 days', 'nights' => 2, 'status' => Booking::STATUS_CANCELLED, 'room' => '104'],
        ];

        foreach ($samples as $s) {
            $room = $rooms->firstWhere('number_name', $s['room']) ?? $rooms->first();
            $checkIn = Carbon::parse($s['in'])->startOfDay();
            $checkOut = (clone $checkIn)->addDays($s['nights']);
            $price = (float) $room->base_price;
            $subtotal = $price * $s['nights'];

            $booking = Booking::updateOrCreate(
                ['guest_email' => $s['email'], 'check_in' => $checkIn->toDateString()],
                [
                    'reference' => strtoupper(Str::random(6)),
                    'guest_name' => $s['guest'],
                    'guest_phone' => null,
                    'check_out' => $checkOut->toDateString(),
                    'number_of_guests' => 2,
                    'status' => $s['status'],
                    'total_price' => $subtotal,
                    'discount_percent' => 0,
                    'payment_status' => $s['status'] === Booking::STATUS_COMPLETED ? Booking::PAYMENT_PAID : Booking::PAYMENT_UNPAID,
                ],
            );

            BookingRoom::updateOrCreate(
                ['booking_id' => $booking->id, 'room_id' => $room->id],
                [
                    'price_per_night' => $price,
                    'nights' => $s['nights'],
                    'subtotal' => $subtotal,
                ],
            );
        }
    }
}
