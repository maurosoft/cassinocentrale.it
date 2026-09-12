<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_booking_page_loads(): void
    {
        $this->get('/prenota')->assertOk()->assertSee('Verifica disponibilità');
    }

    public function test_search_shows_available_rooms(): void
    {
        $in = Carbon::today()->addDays(10)->format('Y-m-d');
        $out = Carbon::today()->addDays(12)->format('Y-m-d');

        $this->get("/prenota?checkin=$in&checkout=$out&guests=2")
            ->assertOk()
            ->assertSee('/ notte');
    }

    public function test_guest_can_submit_booking_request(): void
    {
        $room = Room::active()->first();
        $in = Carbon::today()->addDays(20)->format('Y-m-d');
        $out = Carbon::today()->addDays(22)->format('Y-m-d');

        $response = $this->post('/prenota', [
            'check_in' => $in,
            'check_out' => $out,
            'guests' => 2,
            'rooms' => [$room->slug],
            'guest_first_name' => 'Mario',
            'guest_last_name' => 'Rossi',
            'guest_email' => 'mario@example.com',
            'guest_phone' => '3331234567',
            'privacy' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'guest_email' => 'mario@example.com',
            'status' => Booking::STATUS_DRAFT,
        ]);
    }

    public function test_group_booking_creates_two_rooms(): void
    {
        $rooms = Room::active()->ordered()->take(2)->get();
        $in = Carbon::today()->addDays(40)->format('Y-m-d');
        $out = Carbon::today()->addDays(42)->format('Y-m-d');

        $response = $this->post('/prenota', [
            'check_in' => $in,
            'check_out' => $out,
            'guests' => 3,
            'rooms' => $rooms->pluck('slug')->all(),
            'guest_first_name' => 'Gruppo',
            'guest_last_name' => 'Famiglia',
            'guest_email' => 'gruppo@example.com',
            'guest_phone' => '3339998877',
            'privacy' => '1',
        ]);

        $response->assertRedirect();
        $booking = Booking::where('guest_email', 'gruppo@example.com')->first();
        $this->assertNotNull($booking);
        $this->assertSame(2, $booking->rooms()->count());
    }

    public function test_privacy_is_required(): void
    {
        $room = Room::active()->first();
        $in = Carbon::today()->addDays(20)->format('Y-m-d');
        $out = Carbon::today()->addDays(22)->format('Y-m-d');

        $this->post('/prenota', [
            'check_in' => $in,
            'check_out' => $out,
            'guests' => 2,
            'rooms' => [$room->slug],
            'guest_first_name' => 'Mario',
            'guest_last_name' => 'Rossi',
            'guest_email' => 'mario@example.com',
            'guest_phone' => '3331234567',
        ])->assertSessionHasErrors('privacy');
    }

    public function test_availability_excludes_booked_room(): void
    {
        $room = Room::active()->first();
        $in = Carbon::today()->addDays(30);
        $out = Carbon::today()->addDays(33);

        $booking = Booking::create([
            'reference' => 'CC-TEST01',
            'guest_name' => 'Test',
            'check_in' => $in,
            'check_out' => $out,
            'number_of_guests' => 2,
            'status' => Booking::STATUS_CONFIRMED,
            'total_price' => 130,
        ]);
        $booking->rooms()->create(['room_id' => $room->id, 'price_per_night' => 65, 'nights' => 3, 'subtotal' => 195]);

        $available = app(AvailabilityService::class)->availableRooms($in->copy()->addDay(), $out->copy()->subDay(), 2);

        $this->assertFalse($available->contains('id', $room->id));
    }
}
