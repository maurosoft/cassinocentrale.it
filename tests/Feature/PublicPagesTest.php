<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Cassino');
    }

    public function test_rooms_index_loads(): void
    {
        $this->get('/camere')->assertOk();
    }

    public function test_room_detail_loads(): void
    {
        $room = \App\Models\Room::first();
        $this->get('/camere/'.$room->slug)->assertOk()->assertSee($room->number_name);
    }

    public function test_discover_page_loads(): void
    {
        $this->get('/scopri-cassino')->assertOk();
    }

    public function test_contact_page_loads(): void
    {
        $this->get('/contatti')->assertOk();
    }
}
